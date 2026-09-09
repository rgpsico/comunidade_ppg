<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class DeepSeekArtigoService
{
    /**
     * Gera título, resumo e corpo de um artigo a partir de um tema, via DeepSeek.
     *
     * @return array{titulo: string, slug: string, resumo: string, corpo: string}
     */
    public function gerar(string $tema, ?string $categoria = null): array
    {
        $apiKey = config('services.deepseek.key');

        if (! $apiKey) {
            throw new RuntimeException('DEEP_SEEK_API_KEY não configurada no .env');
        }

        $categoriaTexto = $categoria ? " na categoria \"{$categoria}\"" : '';

        $systemPrompt = "Você é um redator de conteúdo para a plataforma comunitária \"Cria da Comunidade\", "
            . "voltada para profissionais, eventos, projetos sociais, vagas e comércio local em comunidades urbanas.\n"
            . "Escreva SEMPRE em português do Brasil, com tom acolhedor, informativo e próximo da comunidade.\n"
            . "Formato obrigatório da resposta (siga exatamente, sem markdown):\n"
            . "TÍTULO: [título chamativo, até 90 caracteres]\n"
            . "RESUMO: [resumo curto para card, até 280 caracteres]\n"
            . "CORPO: [artigo completo em HTML semântico usando <h2>, <h3>, <p>, <ul>, <li>, <strong>. Mínimo 400 palavras.]";

        $userPrompt = "Escreva um artigo{$categoriaTexto} sobre o seguinte tema: \"{$tema}\"";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(120)->post('https://api.deepseek.com/v1/chat/completions', [
            'model' => config('services.deepseek.model', 'deepseek-chat'),
            'temperature' => 0.7,
            'max_tokens' => 3000,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('Erro na API DeepSeek: '.$response->body());
        }

        $content = $response->json('choices.0.message.content') ?? '';

        if ($content === '') {
            throw new RuntimeException('Resposta vazia da IA.');
        }

        return $this->parsear($content, $tema);
    }

    private function parsear(string $content, string $temaFallback): array
    {
        $titulo = $temaFallback;
        if (preg_match('/T[IÍ]TULO:\s*(.+)/iu', $content, $m)) {
            $titulo = trim($m[1]);
        }

        $resumo = '';
        if (preg_match('/RESUMO:\s*(.+)/iu', $content, $m)) {
            $resumo = trim($m[1]);
        }

        $corpo = $content;
        if (preg_match('/CORPO:\s*([\s\S]+)/iu', $content, $m)) {
            $corpo = trim($m[1]);
        }

        return [
            'titulo' => $titulo,
            'slug' => Str::slug($titulo),
            'resumo' => Str::limit($resumo, 300, ''),
            'corpo' => $corpo,
        ];
    }
}

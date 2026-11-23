<?php

namespace App\Services;

use App\Models\Produto;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Assistente comercial que responde dúvidas sobre UM produto da loja.
 *
 * Tudo o que ele sabe vem das ferramentas abaixo, que leem só o produto aberto e a loja dele
 * (os argumentos que a IA manda não escolhem outro produto). Duas ferramentas não executam nada:
 * - registrar_interesse só pede para a tela mostrar o cartão de confirmação; o lead é gravado
 *   quando o cliente clica (POST /interesse), nunca pela IA.
 * - encaminhar_humano só devolve o botão do WhatsApp da loja.
 */
class AssistenteProduto
{
    private const MAX_RODADAS = 4;

    private const NAO_INFORMADO = 'não informado pela loja';

    /** Ações para a tela (cartão de confirmação, botão do WhatsApp). */
    private array $acoes = [];

    public function __construct(private Produto $produto)
    {
        $this->produto->loadMissing('loja');
    }

    /**
     * @param  list<array{role:string,content:string}>  $mensagens  conversa vinda do navegador
     * @return array{resposta:string,acoes:list<array>}
     */
    public function responder(array $mensagens): array
    {
        $conversa = [['role' => 'system', 'content' => $this->promptSistema()], ...$mensagens];

        for ($rodada = 0; $rodada < self::MAX_RODADAS; $rodada++) {
            $msg = $this->chamarModelo($conversa, final: $rodada === self::MAX_RODADAS - 1);
            $chamadas = $msg['tool_calls'] ?? [];

            if (! $chamadas) {
                return ['resposta' => trim((string) ($msg['content'] ?? '')), 'acoes' => array_values($this->acoes)];
            }

            $conversa[] = ['role' => 'assistant', 'content' => $msg['content'] ?? '', 'tool_calls' => $chamadas];
            foreach ($chamadas as $chamada) {
                $args = json_decode($chamada['function']['arguments'] ?? '{}', true) ?: [];
                $conversa[] = [
                    'role' => 'tool',
                    'tool_call_id' => $chamada['id'],
                    'content' => json_encode($this->executar($chamada['function']['name'] ?? '', $args), JSON_UNESCAPED_UNICODE),
                ];
            }
        }

        throw new RuntimeException('Assistente não chegou a uma resposta.');
    }

    private function chamarModelo(array $conversa, bool $final): array
    {
        $resposta = Http::withToken((string) config('services.deepseek.key'))
            ->timeout(45)
            ->post('https://api.deepseek.com/v1/chat/completions', [
                'model' => config('services.deepseek.model', 'deepseek-chat'),
                'messages' => $conversa,
                'tools' => $this->ferramentas(),
                // Na última rodada obriga a responder em texto, para não ficar em loop de ferramentas.
                'tool_choice' => $final ? 'none' : 'auto',
                'temperature' => 0.3,
                'max_tokens' => 600,
            ])->throw();

        return $resposta->json('choices.0.message') ?? [];
    }

    private function promptSistema(): string
    {
        $p = $this->produto;

        return <<<TXT
Você é o assistente comercial da loja "{$p->loja->nome}" no portal Comunidade PPG e ajuda clientes a conhecer os produtos da loja.

Objetivo: responder dúvidas sobre o produto, características, cores, variações, disponibilidade, entrega, frete, garantia e políticas comerciais; identificar interesse real de compra e encaminhar oportunidades qualificadas para a loja.

Regras:
1. O produto aberto agora é "{$p->nome}" (id {$p->id}). Ele é o contexto principal. Não fale de outros produtos que você não consegue consultar.
2. Consulte as ferramentas sempre que precisar de informação; elas trazem os dados atualizados cadastrados pela loja.
3. Nunca invente preço, estoque, prazo de entrega, garantia, frete ou características. Se a ferramenta disser "{self::NAO_INFORMADO}", diga que a loja não informou isso.
4. Quando não houver informação suficiente, explique a limitação e ofereça falar com a loja pelo WhatsApp (ferramenta encaminhar_humano).
5. Não prometa descontos nem condições comerciais que não estejam nos dados.
6. Responda de forma objetiva, natural e cordial, em português do Brasil, com no máximo 3 frases curtas. Sem markdown.
7. Não peça dados pessoais desnecessários. Não peça nome nem telefone no chat: a tela coleta isso.
8. Quando o cliente mostrar intenção de compra, ofereça encaminhar para a loja.
9. Para passar o contato do cliente à loja, primeiro explique que o nome e o WhatsApp dele serão enviados à loja para ela entrar em contato e pergunte se ele concorda. Só chame registrar_interesse depois que ele disser que sim.
10. registrar_interesse apenas mostra um botão de confirmação na tela. Nunca diga que o contato foi registrado ou enviado: diga que basta ele confirmar no botão.
TXT;
    }

    private function ferramentas(): array
    {
        $f = fn (string $nome, string $desc, array $props = [], array $req = []) => [
            'type' => 'function',
            'function' => [
                'name' => $nome,
                'description' => $desc,
                'parameters' => ['type' => 'object', 'properties' => (object) $props, 'required' => $req],
            ],
        ];

        return [
            $f('consultar_produto', 'Dados do produto aberto: nome, descrição, categoria, preço, preço promocional, disponibilidade, formas de pagamento e endereço da loja.'),
            $f('consultar_variacoes', 'Cores, tamanhos e outras variações do produto aberto.'),
            $f('consultar_estoque', 'Se o produto aberto está disponível e quantas unidades há em estoque.'),
            $f('calcular_frete', 'Como a loja entrega (entrega, retirada, bairros atendidos, taxa) e o prazo de entrega.',
                ['bairro' => ['type' => 'string', 'description' => 'Bairro ou local que o cliente informou, se informou']]),
            $f('consultar_garantia', 'Garantia do produto aberto e política de trocas da loja.'),
            $f('registrar_interesse', 'Mostra ao cliente o botão para confirmar o envio do nome e WhatsApp dele para a loja. Use só depois que o cliente concordou.'),
            $f('encaminhar_humano', 'Mostra ao cliente o botão para falar direto com a loja pelo WhatsApp.',
                ['motivo' => ['type' => 'string', 'description' => 'Resumo curto da dúvida do cliente']]),
        ];
    }

    private function executar(string $nome, array $args): array
    {
        $p = $this->produto;
        $loja = $p->loja;
        $ou = fn ($v) => filled($v) ? $v : self::NAO_INFORMADO;

        return match ($nome) {
            'consultar_produto' => [
                'nome' => $p->nome,
                'descricao' => $ou($p->descricao),
                'categoria' => $ou($p->categoria),
                'preco' => 'R$ ' . number_format($p->preco, 2, ',', '.'),
                'preco_promocional' => $p->preco_promocional ? 'R$ ' . number_format($p->preco_promocional, 2, ',', '.') : 'sem promoção',
                'disponivel' => $p->disponivel ? 'sim' : 'não',
                'formas_pagamento' => $ou($loja->pagamento_info),
                'loja' => $loja->nome,
                'endereco_loja' => $ou($loja->endereco),
            ],
            'consultar_variacoes' => ['variacoes' => $ou($p->variacoes)],
            'consultar_estoque' => [
                'disponivel' => $p->disponivel ? 'sim' : 'não',
                'quantidade_em_estoque' => $p->estoque === null ? self::NAO_INFORMADO : $p->estoque,
            ],
            'calcular_frete' => [
                'bairro_perguntado' => $args['bairro'] ?? null,
                'como_entrega_e_taxas' => $ou($loja->entrega_info),
                'prazo_entrega' => $ou($loja->prazo_entrega),
                'endereco_para_retirada' => $ou($loja->endereco),
                'observacao' => 'Só informe taxa ou prazo se estiverem escritos acima. Não calcule nem estime valores.',
            ],
            'consultar_garantia' => [
                'garantia' => $ou($p->garantia ?: $loja->garantia_padrao),
                'trocas_e_devolucoes' => $ou($loja->trocas_info),
            ],
            'registrar_interesse' => $this->acao('confirmar_interesse', [], [
                'status' => 'botao_de_confirmacao_exibido',
                'instrucao' => 'O contato ainda NÃO foi enviado. Diga ao cliente para preencher e confirmar no botão que apareceu.',
            ]),
            'encaminhar_humano' => $loja->whatsapp
                ? $this->acao('whatsapp', ['url' => $this->linkWhatsapp($args['motivo'] ?? null)], [
                    'status' => 'botao_whatsapp_exibido',
                ])
                : $this->acao('confirmar_interesse', [], [
                    'status' => 'loja_sem_whatsapp',
                    'instrucao' => 'A loja não tem WhatsApp cadastrado; foi exibido o botão para o cliente deixar o contato. Nada foi enviado ainda.',
                ]),
            default => ['erro' => 'ferramenta desconhecida'],
        };
    }

    private function acao(string $tipo, array $dados, array $retorno): array
    {
        $this->acoes[$tipo] = ['tipo' => $tipo, ...$dados];

        return $retorno;
    }

    private function linkWhatsapp(?string $motivo): string
    {
        $fone = preg_replace('/\D/', '', (string) $this->produto->loja->whatsapp);
        if (strlen($fone) <= 11) {
            $fone = '55' . $fone;
        }
        $texto = "Olá! Vi o produto \"{$this->produto->nome}\" no portal Comunidade PPG" . ($motivo ? " e queria saber: {$motivo}" : ' e tenho uma dúvida.');

        return "https://wa.me/{$fone}?text=" . rawurlencode($texto);
    }
}

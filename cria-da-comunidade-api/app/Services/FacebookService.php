<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FacebookService
{
    private string $pageId;
    private string $pageToken;
    private string $graphBase = 'https://graph.facebook.com/v22.0';

    public function __construct()
    {
        $this->pageId    = config('services.facebook.page_id');
        $this->pageToken = config('services.facebook.page_token');
    }

    private function getPageAccessToken(): string
    {
        $response = Http::get("{$this->graphBase}/me/accounts", [
            'access_token' => $this->pageToken,
        ]);

        if ($response->successful()) {
            foreach ($response->json('data', []) as $page) {
                if ((string) $page['id'] === (string) $this->pageId) {
                    return $page['access_token'];
                }
            }
        }

        // fallback: usa o token fornecido diretamente
        return $this->pageToken;
    }

    public function publicarArtigo(string $titulo, string $resumo, string $slug): array
    {
        $url       = "https://ppg.comunidadeppg.com.br/artigos/{$slug}";
        $message   = "📰 {$titulo}\n\n{$resumo}\n\n👉 Leia o artigo completo: {$url}";
        $pageToken = $this->getPageAccessToken();

        $response = Http::post("{$this->graphBase}/{$this->pageId}/feed", [
            'message'      => $message,
            'link'         => $url,
            'access_token' => $pageToken,
        ]);

        if ($response->failed()) {
            return ['sucesso' => false, 'erro' => $response->json('error.message', 'Erro desconhecido')];
        }

        return ['sucesso' => true, 'post_id' => $response->json('id')];
    }
}

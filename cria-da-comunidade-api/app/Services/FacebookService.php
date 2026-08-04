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

    public function publicarArtigo(string $titulo, string $resumo, string $slug): array
    {
        $url     = "https://ppg.comunidadeppg.com.br/artigos/{$slug}";
        $message = "📰 {$titulo}\n\n{$resumo}\n\n👉 Leia o artigo completo: {$url}";

        $response = Http::post("{$this->graphBase}/{$this->pageId}/feed", [
            'message'      => $message,
            'link'         => $url,
            'access_token' => $this->pageToken,
        ]);

        if ($response->failed()) {
            return ['sucesso' => false, 'erro' => $response->json('error.message', 'Erro desconhecido')];
        }

        return ['sucesso' => true, 'post_id' => $response->json('id')];
    }
}

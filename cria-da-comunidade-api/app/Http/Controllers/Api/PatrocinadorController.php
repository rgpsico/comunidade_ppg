<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patrocinador;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatrocinadorController extends Controller
{
    public function ativo(Request $request): JsonResponse
    {
        $comunidadeId = $request->query('comunidade_id');

        $patrocinador = Patrocinador::ativo()
            ->when($comunidadeId, fn ($q) => $q->where('comunidade_id', $comunidadeId))
            ->latest()
            ->first();

        if (! $patrocinador) {
            return response()->json(null);
        }

        return response()->json([
            'id'          => (string) $patrocinador->id,
            'nome'        => $patrocinador->nome,
            'imagem_url'  => $patrocinador->imagem_url,
            'texto'       => $patrocinador->texto,
            'link_url'    => $patrocinador->link_url,
            'texto_botao' => $patrocinador->texto_botao,
        ]);
    }
}

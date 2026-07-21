<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artigo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArtigoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Artigo::where('publicado', true)
            ->orderByDesc('publicado_em');

        if ($request->filled('comunidade_id')) {
            $query->where('comunidade_id', $request->comunidade_id);
        }
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('titulo', 'like', "%{$request->search}%")
                  ->orWhere('resumo', 'like', "%{$request->search}%");
            });
        }

        $perPage = min((int) ($request->per_page ?? 20), 50);
        return response()->json($query->paginate($perPage));
    }

    public function show(Artigo $artigo): JsonResponse
    {
        if (!$artigo->publicado) {
            abort(404);
        }
        return response()->json($artigo->load('comunidade:id,nome,slug'));
    }
}

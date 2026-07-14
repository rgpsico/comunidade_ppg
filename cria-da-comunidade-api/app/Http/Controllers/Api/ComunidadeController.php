<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comunidade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComunidadeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Comunidade::where('ativa', true);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('search')) {
            $query->where('nome', 'like', "%{$request->search}%");
        }

        return response()->json($query->orderBy('nome')->get(['id', 'nome', 'slug', 'cidade', 'estado', 'imagem']));
    }

    public function show(Comunidade $comunidade): JsonResponse
    {
        $comunidade->loadCount(['profissionais', 'eventos', 'projetos', 'vagas', 'users']);
        return response()->json($comunidade);
    }
}

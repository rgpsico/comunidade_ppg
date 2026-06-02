<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Apoio;
use App\Models\Projeto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjetoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Projeto::with(['comunidade:id,nome'])
            ->where('ativo', true);

        if ($request->filled('causa')) {
            $query->where('causa', $request->causa);
        }
        if ($request->filled('comunidade_id')) {
            $query->where('comunidade_id', $request->comunidade_id);
        }

        return response()->json($query->orderBy('nome')->paginate(12));
    }

    public function show(Projeto $projeto): JsonResponse
    {
        return response()->json(
            $projeto->load(['comunidade', 'responsavel:id,name', 'apoios'])
        );
    }

    public function apoiar(Request $request, Projeto $projeto): JsonResponse
    {
        $data = $request->validate([
            'valor'           => 'required|numeric|min:1',
            'forma_pagamento' => 'required|in:pix,cartao,boleto',
            'nome_apoiador'   => 'nullable|string|max:100',
        ]);

        $apoio = Apoio::create([
            ...$data,
            'projeto_id'   => $projeto->id,
            'user_id'      => auth()->id(),
            'status'       => 'confirmado',
        ]);

        // Atualizar arrecadado e progresso
        $totalArrecadado = $projeto->apoios()->where('status', 'confirmado')->sum('valor');
        $progresso = $projeto->meta > 0 ? min(100, (int) round(($totalArrecadado / $projeto->meta) * 100)) : 0;

        $projeto->update([
            'arrecadado' => $totalArrecadado,
            'progresso'  => $progresso,
        ]);

        return response()->json($apoio, 201);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nome'           => 'required|string|max:150',
            'descricao'      => 'nullable|string',
            'icone'          => 'nullable|string|max:10',
            'causa'          => 'required|string',
            'cor'            => 'nullable|string|max:7',
            'meta'           => 'nullable|numeric|min:0',
            'impacto_valor'  => 'nullable|string',
            'impacto_label'  => 'nullable|string',
            'anos_atuando'   => 'nullable|integer|min:0',
            'cta_label'      => 'nullable|string|max:60',
            'aceita_doacoes' => 'nullable|boolean',
            'comunidade_id'  => 'nullable|exists:comunidades,id',
        ]);

        $projeto = Projeto::create([
            ...$data,
            'responsavel_id' => auth()->id(),
            'ativo'          => true,
        ]);

        return response()->json($projeto, 201);
    }

    public function update(Request $request, Projeto $projeto): JsonResponse
    {
        $data = $request->validate([
            'nome'           => 'sometimes|required|string|max:150',
            'descricao'      => 'nullable|string',
            'icone'          => 'nullable|string|max:10',
            'causa'          => 'sometimes|required|string',
            'cor'            => 'nullable|string|max:7',
            'meta'           => 'nullable|numeric|min:0',
            'impacto_valor'  => 'nullable|string',
            'impacto_label'  => 'nullable|string',
            'anos_atuando'   => 'nullable|integer|min:0',
            'cta_label'      => 'nullable|string|max:60',
            'aceita_doacoes' => 'nullable|boolean',
            'ativo'          => 'nullable|boolean',
            'comunidade_id'  => 'nullable|exists:comunidades,id',
        ]);

        $projeto->update($data);

        return response()->json($projeto->fresh());
    }

    public function destroy(Projeto $projeto): JsonResponse
    {
        $this->authorize('delete', $projeto);
        $projeto->delete();
        return response()->json(null, 204);
    }
}

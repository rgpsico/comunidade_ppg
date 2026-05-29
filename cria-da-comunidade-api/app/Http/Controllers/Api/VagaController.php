<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vaga;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VagaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Vaga::with(['comunidade:id,nome'])
            ->where('ativa', true);

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('comunidade_id')) {
            $query->where('comunidade_id', $request->comunidade_id);
        }
        if ($request->boolean('urgente')) {
            $query->where('urgente', true);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('titulo', 'like', "%{$request->search}%")
                  ->orWhere('empresa', 'like', "%{$request->search}%");
            });
        }

        return response()->json($query->orderByDesc('urgente')->orderByDesc('created_at')->paginate(12));
    }

    public function show(Vaga $vaga): JsonResponse
    {
        return response()->json(
            $vaga->load(['comunidade:id,nome', 'requisitos', 'beneficios', 'anunciante:id,name'])
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'titulo'          => 'required|string|max:150',
            'empresa'         => 'required|string|max:100',
            'descricao'       => 'nullable|string',
            'local'           => 'nullable|string|max:200',
            'tipo'            => 'required|in:CLT,PJ,Freelance,Estágio,Voluntário',
            'salario'         => 'nullable|string|max:50',
            'salario_periodo' => 'nullable|in:hora,dia,semana,mês,ano',
            'logo_cor'        => 'nullable|string|max:7',
            'logo_iniciais'   => 'nullable|string|max:5',
            'whatsapp'        => 'nullable|string|max:20',
            'email_contato'   => 'nullable|email|max:150',
            'urgente'         => 'boolean',
            'comunidade_id'   => 'nullable|exists:comunidades,id',
            'requisitos'      => 'nullable|array',
            'requisitos.*.descricao' => 'required|string',
            'requisitos.*.nivel'     => 'required|in:obrigatório,desejável,opcional',
            'beneficios'      => 'nullable|array',
            'beneficios.*.descricao' => 'required|string',
        ]);

        $vaga = Vaga::create([
            ...\Arr::except($data, ['requisitos', 'beneficios']),
            'anunciante_id' => auth()->id(),
        ]);

        foreach ($data['requisitos'] ?? [] as $i => $req) {
            $vaga->requisitos()->create([...$req, 'ordem' => $i]);
        }

        foreach ($data['beneficios'] ?? [] as $i => $ben) {
            $vaga->beneficios()->create([...$ben, 'ordem' => $i]);
        }

        return response()->json($vaga->load(['requisitos', 'beneficios']), 201);
    }

    public function update(Request $request, Vaga $vaga): JsonResponse
    {
        $this->authorize('update', $vaga);

        $data = $request->validate([
            'titulo'          => 'sometimes|string|max:150',
            'empresa'         => 'sometimes|string|max:100',
            'descricao'       => 'nullable|string',
            'local'           => 'nullable|string|max:200',
            'tipo'            => 'sometimes|in:CLT,PJ,Freelance,Estágio,Voluntário',
            'salario'         => 'nullable|string|max:50',
            'salario_periodo' => 'nullable|in:hora,dia,semana,mês,ano',
            'logo_cor'        => 'nullable|string|max:7',
            'logo_iniciais'   => 'nullable|string|max:5',
            'whatsapp'        => 'nullable|string|max:20',
            'email_contato'   => 'nullable|email|max:150',
            'urgente'         => 'boolean',
            'ativa'           => 'boolean',
        ]);

        $vaga->update($data);

        return response()->json($vaga->load(['requisitos', 'beneficios']));
    }

    public function destroy(Vaga $vaga): JsonResponse
    {
        $this->authorize('delete', $vaga);
        $vaga->delete();
        return response()->json(null, 204);
    }

    public function candidatar(Request $request, Vaga $vaga): JsonResponse
    {
        $vaga->increment('candidatos');

        return response()->json(['message' => 'Candidatura registrada.', 'candidatos' => $vaga->fresh()->candidatos]);
    }
}

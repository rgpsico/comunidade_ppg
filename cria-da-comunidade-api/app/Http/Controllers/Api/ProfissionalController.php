<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profissional;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfissionalController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Profissional::with('comunidade')
            ->where('ativo', true);

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }
        if ($request->filled('comunidade_id')) {
            $query->where('comunidade_id', $request->comunidade_id);
        }
        if ($request->boolean('verificado')) {
            $query->where('verificado', true);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nome', 'like', "%{$request->search}%")
                  ->orWhere('cargo', 'like', "%{$request->search}%");
            });
        }

        $sort = $request->get('sort', 'estrelas');
        $query->orderBy($sort === 'preco' ? 'preco_a_partir' : 'estrelas', 'desc');

        return response()->json($query->paginate(16));
    }

    public function show(Profissional $profissional): JsonResponse
    {
        return response()->json($profissional->load('comunidade', 'user'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nome'           => 'required|string|max:100',
            'categoria'      => 'required|string',
            'cargo'          => 'required|string',
            'bio'            => 'nullable|string',
            'whatsapp'       => 'required|string|max:20',
            'comunidade_id'  => 'nullable|exists:comunidades,id',
            'preco_a_partir' => 'nullable|numeric|min:0',
            'tags'           => 'nullable|array',
            'cor1'           => 'nullable|string|max:7',
            'cor2'           => 'nullable|string|max:7',
            'foto'           => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('profissionais/fotos', 'public');
        }

        $profissional = Profissional::create($data);

        return response()->json($profissional, 201);
    }

    public function update(Request $request, Profissional $profissional): JsonResponse
    {
        $this->authorize('update', $profissional);

        $profissional->update($request->validate([
            'nome'           => 'sometimes|string|max:100',
            'categoria'      => 'sometimes|string',
            'cargo'          => 'sometimes|string',
            'bio'            => 'nullable|string',
            'whatsapp'       => 'nullable|string|max:20',
            'preco_a_partir' => 'nullable|numeric|min:0',
            'tags'           => 'nullable|array',
            'cor1'           => 'nullable|string|max:7',
            'cor2'           => 'nullable|string|max:7',
            'ativo'          => 'sometimes|boolean',
        ]));

        return response()->json($profissional);
    }

    public function destroy(Profissional $profissional): JsonResponse
    {
        $this->authorize('delete', $profissional);
        $profissional->delete();
        return response()->json(null, 204);
    }
}

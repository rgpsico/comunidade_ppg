<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loja;
use App\Models\Produto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdutoController extends Controller
{
    private const MAX_FOTOS = 10;

    public function store(Request $request, Loja $loja): JsonResponse
    {
        if ($loja->user_id !== auth()->id()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $data = $request->validate([
            'nome'              => 'required|string|max:150',
            'descricao'         => 'nullable|string',
            'preco'             => 'required|numeric|min:0',
            'preco_promocional' => 'nullable|numeric|min:0',
            'categoria'         => 'nullable|string|max:80',
            'disponivel'        => 'nullable|boolean',
            'destaque'          => 'nullable|boolean',
            'ordem'             => 'nullable|integer',
            'imagens'           => 'nullable|array|max:' . self::MAX_FOTOS,
            'imagens.*'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ], ['imagens.max' => 'Cada produto pode ter até ' . self::MAX_FOTOS . ' fotos.']);

        $paths = [];
        if ($request->hasFile('imagens')) {
            foreach ($request->file('imagens') as $img) {
                $paths[] = $img->store("produtos/imagens/{$loja->id}", 'public');
            }
        }

        $produto = $loja->produtos()->create([
            ...collect($data)->except('imagens')->toArray(),
            'imagens'    => $paths ?: null,
            'disponivel' => $request->boolean('disponivel', true),
            'destaque'   => $request->boolean('destaque', false),
            'ordem'      => $data['ordem'] ?? ($loja->produtos()->max('ordem') + 1),
        ]);

        return response()->json($produto->fresh(), 201);
    }

    public function update(Request $request, Produto $produto): JsonResponse
    {
        if ($produto->loja->user_id !== auth()->id()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $data = $request->validate([
            'nome'              => 'sometimes|string|max:150',
            'descricao'         => 'nullable|string',
            'preco'             => 'sometimes|numeric|min:0',
            'preco_promocional' => 'nullable|numeric|min:0',
            'categoria'         => 'nullable|string|max:80',
            'disponivel'        => 'nullable|boolean',
            'destaque'          => 'nullable|boolean',
            'ordem'             => 'nullable|integer',
            'imagens_manter'    => 'nullable|array',   // paths a preservar
            'imagens_manter.*'  => 'nullable|string',
            'imagens.*'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        // Imagens a manter: o app manda a URL (ou o caminho) de cada foto que ficou.
        // Só vale o que já era deste produto; volta a guardar o caminho no disco, não a URL.
        $atuais = $produto->imagens ?? [];
        $porChave = [];
        foreach ($atuais as $p) {
            $porChave[$p] = $p;
            $porChave[str_starts_with($p, 'http') ? $p : Storage::disk('public')->url($p)] = $p;
        }
        $paths = ($request->has('imagens_manter') || $request->boolean('imagens_editadas'))
            ? collect($data['imagens_manter'] ?? [])->map(fn ($v) => $porChave[$v] ?? null)->filter()->unique()->values()->all()
            : $atuais;

        $novas = $request->file('imagens') ?? [];
        if (count($paths) + count($novas) > self::MAX_FOTOS) {
            return response()->json(['message' => 'Cada produto pode ter até ' . self::MAX_FOTOS . ' fotos.'], 422);
        }
        foreach ($novas as $img) {
            $paths[] = $img->store("produtos/imagens/{$produto->loja_id}", 'public');
        }

        // Fotos tiradas do produto: apaga o arquivo.
        foreach (array_diff($atuais, $paths) as $removida) {
            if (! str_starts_with($removida, 'http')) {
                Storage::disk('public')->delete($removida);
            }
        }

        $produto->update([
            ...collect($data)->except(['imagens', 'imagens_manter'])->toArray(),
            'imagens' => $paths ?: null,
        ]);

        return response()->json($produto->fresh());
    }

    public function destroy(Produto $produto): JsonResponse
    {
        if ($produto->loja->user_id !== auth()->id()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        // Remove arquivos de imagem do storage
        foreach ($produto->imagens ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }

        $produto->delete();

        return response()->json(null, 204);
    }
}

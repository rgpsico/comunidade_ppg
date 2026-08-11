<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Curriculo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CurriculoController extends Controller
{
    public function meu(Request $request): JsonResponse
    {
        $curriculo = Curriculo::where('user_id', auth()->id())->first();
        return response()->json($curriculo);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nome'           => 'required|string|max:150',
            'email'          => 'required|email|max:150',
            'telefone'       => 'nullable|string|max:20',
            'area_atuacao'   => 'required|string|max:100',
            'habilidades'    => 'nullable|array',
            'habilidades.*'  => 'string|max:60',
            'experiencia'    => 'nullable|string|max:2000',
            'cidade'         => 'nullable|string|max:100',
            'disponibilidade'=> 'nullable|in:imediata,30 dias,60 dias',
            'comunidade_id'  => 'nullable|exists:comunidades,id',
        ]);

        $existing = Curriculo::where('user_id', auth()->id())->first();

        if ($existing) {
            $existing->update($data);
            return response()->json($existing);
        }

        $curriculo = Curriculo::create([
            ...$data,
            'user_id' => auth()->id(),
        ]);

        return response()->json($curriculo, 201);
    }

    public function uploadPdf(Request $request): JsonResponse
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:5120',
        ]);

        $existing = Curriculo::where('user_id', auth()->id())->first();

        if (! $existing) {
            return response()->json(['message' => 'Cadastre seu currículo antes de enviar o PDF.'], 422);
        }

        if ($existing->pdf_path) {
            Storage::disk('public')->delete($existing->pdf_path);
        }

        $path = $request->file('pdf')->store('curriculos/pdfs', 'public');
        $existing->update(['pdf_path' => $path]);

        return response()->json(['pdf_url' => $existing->fresh()->pdf_url]);
    }

    public function update(Request $request): JsonResponse
    {
        $curriculo = Curriculo::where('user_id', auth()->id())->firstOrFail();

        $data = $request->validate([
            'nome'           => 'sometimes|string|max:150',
            'email'          => 'sometimes|email|max:150',
            'telefone'       => 'nullable|string|max:20',
            'area_atuacao'   => 'sometimes|string|max:100',
            'habilidades'    => 'nullable|array',
            'habilidades.*'  => 'string|max:60',
            'experiencia'    => 'nullable|string|max:2000',
            'cidade'         => 'nullable|string|max:100',
            'disponibilidade'=> 'nullable|in:imediata,30 dias,60 dias',
            'publicado'      => 'boolean',
        ]);

        $curriculo->update($data);
        return response()->json($curriculo->fresh());
    }

    public function destroy(): JsonResponse
    {
        $curriculo = Curriculo::where('user_id', auth()->id())->firstOrFail();

        if ($curriculo->pdf_path) {
            Storage::disk('public')->delete($curriculo->pdf_path);
        }

        $curriculo->delete();
        return response()->json(null, 204);
    }
}

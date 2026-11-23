<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use App\Services\AssistenteProduto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class AssistenteProdutoController extends Controller
{
    public function __invoke(Request $request, Produto $produto): JsonResponse
    {
        $data = $request->validate([
            'mensagens'           => 'required|array|min:1|max:20',
            'mensagens.*.role'    => 'required|in:user,assistant',
            'mensagens.*.content' => 'required|string|max:600',
        ]);

        if (end($data['mensagens'])['role'] !== 'user') {
            return response()->json(['message' => 'A última mensagem precisa ser do cliente.'], 422);
        }
        if (! $produto->loja?->ativo || ! filled(config('services.deepseek.key'))) {
            return response()->json(['message' => 'Assistente indisponível para este produto.'], 503);
        }

        try {
            $mensagens = array_map(fn ($m) => ['role' => $m['role'], 'content' => $m['content']], $data['mensagens']);

            return response()->json((new AssistenteProduto($produto))->responder($mensagens));
        } catch (Throwable $e) {
            report($e);

            return response()->json(['message' => 'O assistente não conseguiu responder agora. Tente de novo ou fale com a loja.'], 503);
        }
    }
}

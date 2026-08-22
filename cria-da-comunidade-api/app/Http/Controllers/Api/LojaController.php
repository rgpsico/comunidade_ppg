<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loja;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LojaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Loja::with('comunidade')
            ->withCount('produtos')
            ->where('ativo', true);

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('comunidade_id')) {
            $query->where('comunidade_id', $request->comunidade_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nome', 'like', "%{$request->search}%")
                  ->orWhere('descricao', 'like', "%{$request->search}%");
            });
        }

        $query->orderBy('verificado', 'desc')->orderBy('nome');

        return response()->json($query->paginate((int) $request->get('per_page', 30)));
    }

    public function show(Loja $loja): JsonResponse
    {
        return response()->json(
            $loja->load(['produtos' => fn ($q) => $q->where('disponivel', true), 'comunidade'])
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nome'          => 'required|string|max:150',
            'categoria'     => 'required|string|max:80',
            'descricao'     => 'nullable|string',
            'whatsapp'      => 'required|string|max:20',
            'endereco'      => 'nullable|string|max:200',
            'comunidade_id' => 'nullable|exists:comunidades,id',
            'cor1'          => 'nullable|string|max:7',
            'cor2'          => 'nullable|string|max:7',
            'logo'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'capa'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('lojas/logos', 'public');
        }
        if ($request->hasFile('capa')) {
            $data['capa'] = $request->file('capa')->store('lojas/capas', 'public');
        }

        $data['user_id'] = $request->user()?->id;

        return response()->json(Loja::create($data), 201);
    }

    public function update(Request $request, Loja $loja): JsonResponse
    {
        $this->authorize('update', $loja);

        $loja->update($request->validate([
            'nome'      => 'sometimes|string|max:150',
            'categoria' => 'sometimes|string|max:80',
            'descricao' => 'nullable|string',
            'whatsapp'  => 'nullable|string|max:20',
            'endereco'  => 'nullable|string|max:200',
            'cor1'      => 'nullable|string|max:7',
            'cor2'      => 'nullable|string|max:7',
            'ativo'     => 'sometimes|boolean',
        ]));

        return response()->json($loja);
    }

    public function destroy(Loja $loja): JsonResponse
    {
        $this->authorize('delete', $loja);
        $loja->delete();
        return response()->json(null, 204);
    }

    public function interesse(Request $request): JsonResponse
    {
        $data = $request->validate([
            'produto_nome'     => 'required|string|max:200',
            'produto_preco'    => 'required|string|max:50',
            'loja_nome'        => 'required|string|max:200',
            'loja_id'          => 'required|string',
            'comprador_nome'   => 'required|string|max:120',
            'comprador_fone'   => 'required|string|max:30',
        ]);

        $preco    = $data['produto_preco'];
        $produto  = $data['produto_nome'];
        $loja     = $data['loja_nome'];
        $lojaId   = $data['loja_id'];
        $nome     = $data['comprador_nome'];
        $fone     = $data['comprador_fone'];
        $wppLink  = 'https://wa.me/55' . preg_replace('/\D/', '', $fone);

        Mail::raw(
            "🛍 Novo interesse em produto!\n\n" .
            "Produto: {$produto}\n" .
            "Preço: {$preco}\n" .
            "Loja: {$loja} (ID: {$lojaId})\n\n" .
            "━━━━━━━━━━━━━━━━━━\n" .
            "Comprador: {$nome}\n" .
            "Telefone: {$fone}\n" .
            "WhatsApp: {$wppLink}\n" .
            "━━━━━━━━━━━━━━━━━━\n\n" .
            "Data: " . now()->format('d/m/Y H:i'),
            function ($msg) use ($produto, $loja, $nome) {
                $msg->to('rogernevesn@gmail.com')
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject("Interesse: {$produto} — {$loja} (comprador: {$nome})");
            }
        );

        return response()->json(['ok' => true]);
    }
}

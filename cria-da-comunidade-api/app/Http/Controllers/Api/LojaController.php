<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loja;
use App\Models\ProdutoInteresse;
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
        // Não existe LojaPolicy: só o dono (ou admin do portal) edita a loja.
        if ($loja->user_id !== auth()->id() && ! auth()->user()?->hasRole(['super_admin', 'admin'])) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $loja->update($request->validate([
            'nome'      => 'sometimes|string|max:150',
            'categoria' => 'sometimes|string|max:80',
            'descricao' => 'nullable|string',
            'whatsapp'  => 'nullable|string|max:20',
            'endereco'  => 'nullable|string|max:200',
            'cor1'      => 'nullable|string|max:7',
            'cor2'      => 'nullable|string|max:7',
            'ativo'     => 'sometimes|boolean',
            // Políticas usadas pelo assistente dos produtos
            'garantia_padrao' => 'nullable|string|max:300',
            'prazo_entrega'   => 'nullable|string|max:300',
            'entrega_info'    => 'nullable|string|max:2000',
            'trocas_info'     => 'nullable|string|max:2000',
            'pagamento_info'  => 'nullable|string|max:300',
        ]));

        return response()->json($loja);
    }

    public function destroy(Loja $loja): JsonResponse
    {
        if ($loja->user_id !== auth()->id()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }
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
            'produto_id'       => 'nullable|integer',
            'origem'           => 'nullable|in:botao,assistente',
        ]);

        // Guarda o interessado (antes só ia o e-mail). Só registra se loja/produto existem de verdade.
        $lojaModel = Loja::find((int) $data['loja_id']);
        if ($lojaModel) {
            $produtoModel = isset($data['produto_id'])
                ? $lojaModel->produtos()->whereKey($data['produto_id'])->first()
                : null;
            ProdutoInteresse::create([
                'loja_id'    => $lojaModel->id,
                'produto_id' => $produtoModel?->id,
                'nome'       => $data['comprador_nome'],
                'telefone'   => $data['comprador_fone'],
                'origem'     => $data['origem'] ?? 'botao',
            ]);
        }

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
            "Loja: {$loja} (ID: {$lojaId})\n" .
            "Origem: " . (($data['origem'] ?? 'botao') === 'assistente' ? 'assistente do produto' : 'botão Tenho interesse') . "\n\n" .
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

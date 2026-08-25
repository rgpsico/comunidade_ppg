<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chamado;
use App\Models\ChamadoDoacao;
use App\Models\Profissional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ChamadoController extends Controller
{
    public function index(Request $request)
    {
        $query = Chamado::with(['user:id,name', 'profissional:id,nome,foto_url'])
            ->latest();

        if ($request->filled('comunidade_id')) {
            $query->where('comunidade_id', $request->comunidade_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $chamados = $query->paginate($request->integer('per_page', 30));

        return response()->json([
            'data' => $chamados->items(),
            'total' => $chamados->total(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo'             => 'required|in:problema,servico',
            'titulo'           => 'required|string|max:200',
            'descricao'        => 'required|string|max:2000',
            'categoria'        => 'required|string|max:80',
            'local'            => 'nullable|string|max:200',
            'estimativa_valor' => 'nullable|string|max:100',
            'urgencia'         => 'required|in:normal,urgente,critico',
            'comunidade_id'    => 'nullable|integer|exists:comunidades,id',
        ]);

        $data['user_id'] = auth()->id();

        $chamado = Chamado::create($data);
        $chamado->load('user:id,name');

        // Notify matching professionals
        $this->notificarProfissionais($chamado);

        // Notify admin
        $this->notificarAdmin($chamado);

        return response()->json($chamado, 201);
    }

    public function show(int $id)
    {
        $chamado = Chamado::with([
            'user:id,name',
            'profissional:id,nome,foto_url,whatsapp',
            'doacoes.user:id,name',
        ])->findOrFail($id);

        return response()->json(array_merge($chamado->toArray(), [
            'total_doacoes' => $chamado->doacoes->sum('valor'),
        ]));
    }

    public function aceitar(Request $request, int $id)
    {
        $chamado = Chamado::findOrFail($id);

        if ($chamado->status !== 'aberto') {
            return response()->json(['message' => 'Este chamado não está aberto.'], 422);
        }

        $profissional = Profissional::where('user_id', auth()->id())->first();
        if (! $profissional) {
            return response()->json(['message' => 'Você precisa ter um perfil profissional para aceitar chamados.'], 422);
        }

        $chamado->update([
            'profissional_id' => $profissional->id,
            'status'          => 'aceito',
            'aceito_em'       => now(),
        ]);

        // Notify creator
        $criador = $chamado->user;
        if ($criador?->email) {
            $msg = "Olá {$criador->name}!\n\n"
                . "Boas notícias! O profissional {$profissional->nome} aceitou seu chamado \"{$chamado->titulo}\".\n\n"
                . "Entre em contato com ele para combinar os detalhes.\n\n"
                . "WhatsApp: " . ($profissional->whatsapp ? "https://wa.me/55" . preg_replace('/\D/', '', $profissional->whatsapp) : 'Não informado') . "\n\n"
                . "Cria da Comunidade";

            Mail::raw($msg, fn ($m) => $m
                ->to($criador->email, $criador->name)
                ->from('contato@pilatesgestao.com.br', 'Cria da Comunidade')
                ->subject("✅ Chamado aceito: {$chamado->titulo}")
            );
        }

        $chamado->load(['user:id,name', 'profissional:id,nome,foto_url,whatsapp', 'doacoes.user:id,name']);
        return response()->json($chamado);
    }

    public function resolver(Request $request, int $id)
    {
        $chamado = Chamado::with('profissional')->findOrFail($id);
        $userId = auth()->id();

        $isCreator = $chamado->user_id === $userId;
        $isPro = $chamado->profissional?->user_id === $userId;

        if (! $isCreator && ! $isPro) {
            return response()->json(['message' => 'Apenas o criador ou o profissional responsável pode marcar como resolvido.'], 403);
        }

        if (! in_array($chamado->status, ['aceito', 'em_andamento'])) {
            return response()->json(['message' => 'Este chamado não pode ser resolvido no status atual.'], 422);
        }

        $chamado->update([
            'status'       => 'resolvido',
            'resolvido_em' => now(),
        ]);

        $chamado->load(['user:id,name', 'profissional:id,nome,foto_url,whatsapp', 'doacoes.user:id,name']);
        return response()->json($chamado);
    }

    public function doacao(Request $request, int $id)
    {
        $chamado = Chamado::findOrFail($id);

        $data = $request->validate([
            'valor'    => 'required|numeric|min:1',
            'mensagem' => 'nullable|string|max:500',
        ]);

        $doacao = ChamadoDoacao::create([
            'chamado_id' => $chamado->id,
            'user_id'    => auth()->id(),
            'valor'      => $data['valor'],
            'mensagem'   => $data['mensagem'] ?? null,
        ]);

        // Notify admin
        $user = auth()->user();
        $msg = "Nova doação registrada!\n\n"
            . "Chamado: {$chamado->titulo}\n"
            . "Doador: {$user->name}\n"
            . "Valor: R$ " . number_format($data['valor'], 2, ',', '.') . "\n"
            . "Mensagem: " . ($data['mensagem'] ?? '-') . "\n\n"
            . "⚠️ Lembre-se: o pagamento é externo (PIX/dinheiro). Confirme com o doador.";

        Mail::raw($msg, fn ($m) => $m
            ->to('rogernevesn@gmail.com', 'Admin')
            ->from('contato@pilatesgestao.com.br', 'Cria da Comunidade')
            ->subject("💰 Doação: {$chamado->titulo}")
        );

        $doacao->load('user:id,name');
        return response()->json($doacao, 201);
    }

    public function ranking()
    {
        // Pontos de profissionais que resolveram chamados (3 pts cada)
        $profRanking = DB::table('chamados')
            ->join('profissionais', 'chamados.profissional_id', '=', 'profissionais.id')
            ->join('users', 'profissionais.user_id', '=', 'users.id')
            ->where('chamados.status', 'resolvido')
            ->whereNotNull('profissionais.user_id')
            ->groupBy('users.id', 'users.name')
            ->selectRaw('users.id as user_id, users.name, COUNT(*) * 3 as pontos, COUNT(*) as chamados_ajudados, 0 as total_doado')
            ->get();

        // Pontos de doadores (2 pts cada doação)
        $doaRanking = DB::table('chamado_doacoes')
            ->join('users', 'chamado_doacoes.user_id', '=', 'users.id')
            ->groupBy('users.id', 'users.name')
            ->selectRaw('users.id as user_id, users.name, COUNT(*) * 2 as pontos, 0 as chamados_ajudados, SUM(valor) as total_doado')
            ->get();

        $merged = collect($profRanking)
            ->concat($doaRanking)
            ->groupBy('user_id')
            ->map(fn ($rows) => [
                'user_id'          => $rows->first()->user_id,
                'name'             => $rows->first()->name,
                'pontos'           => $rows->sum('pontos'),
                'chamados_ajudados' => $rows->sum('chamados_ajudados'),
                'total_doado'      => (float) $rows->sum('total_doado'),
            ])
            ->sortByDesc('pontos')
            ->values()
            ->take(10);

        return response()->json($merged);
    }

    private function notificarProfissionais(Chamado $chamado): void
    {
        $categoriaMap = [
            'Beleza'         => ['Beleza'],
            'Construção'     => ['Construção'],
            'Elétrica'       => ['Construção'],
            'Hidráulica'     => ['Construção'],
            'Casa & Limpeza' => ['Casa'],
            'Transporte'     => ['Transporte'],
            'Saúde'          => ['Saúde'],
            'Eventos'        => ['Eventos'],
        ];

        $categoriasAlvo = $categoriaMap[$chamado->categoria] ?? [];

        $query = Profissional::whereNotNull('user_id')->with('user');
        if (! empty($categoriasAlvo)) {
            $query->whereIn('categoria', $categoriasAlvo);
        }

        $profissionais = $query->limit(50)->get();

        foreach ($profissionais as $prof) {
            if (! $prof->user?->email) continue;

            $urgenciaLabel = ['normal' => 'Normal', 'urgente' => '⚠️ URGENTE', 'critico' => '🚨 CRÍTICO'][$chamado->urgencia] ?? '';

            $msg = "Olá {$prof->nome}!\n\n"
                . "Um novo chamado foi registrado na sua área ({$chamado->categoria}) — {$urgenciaLabel}.\n\n"
                . "Título: {$chamado->titulo}\n"
                . "Descrição: {$chamado->descricao}\n"
                . ($chamado->local ? "Local: {$chamado->local}\n" : '')
                . ($chamado->estimativa_valor ? "Estimativa: {$chamado->estimativa_valor}\n" : '')
                . "\nAcesse a plataforma para aceitar este chamado:\nhttps://ppg.comunidadeppg.com.br\n\n"
                . "Cria da Comunidade";

            Mail::raw($msg, fn ($m) => $m
                ->to($prof->user->email, $prof->nome)
                ->from('contato@pilatesgestao.com.br', 'Cria da Comunidade')
                ->subject("📢 Novo chamado na sua área: {$chamado->titulo}")
            );
        }
    }

    private function notificarAdmin(Chamado $chamado): void
    {
        $urgencia = ['normal' => 'Normal', 'urgente' => 'URGENTE', 'critico' => 'CRÍTICO'][$chamado->urgencia] ?? '';
        $tipo = $chamado->tipo === 'problema' ? 'Problema' : 'Serviço';

        $msg = "[Novo Chamado] {$chamado->titulo}\n\n"
            . "Tipo: {$tipo} | Urgência: {$urgencia}\n"
            . "Categoria: {$chamado->categoria}\n"
            . "Local: " . ($chamado->local ?? '-') . "\n"
            . "Estimativa: " . ($chamado->estimativa_valor ?? '-') . "\n\n"
            . "Descrição:\n{$chamado->descricao}\n\n"
            . "Criado por: {$chamado->user?->name} (ID #{$chamado->user_id})";

        Mail::raw($msg, fn ($m) => $m
            ->to('rogernevesn@gmail.com', 'Admin')
            ->from('contato@pilatesgestao.com.br', 'Cria da Comunidade')
            ->subject("[Chamado {$urgencia}] {$chamado->titulo}")
        );
    }
}

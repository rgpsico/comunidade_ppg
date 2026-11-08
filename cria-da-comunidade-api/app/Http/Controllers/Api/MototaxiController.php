<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CadastroProfissional;
use App\Models\Comunidade;
use App\Models\Profissional;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Mototáxi da comunidade: cadastro por link (público), lista com status online e o painel do mototaxista.
 * O painel não tem login: o link com o token é a chave. Ele avisa "estou rodando" a cada 30s (ping) e,
 * sem aviso por ONLINE_SEGUNDOS, o mototaxista aparece como offline sozinho.
 */
class MototaxiController extends Controller
{
    /** Lista pública: só aprovados, online primeiro. Posição e token nunca saem daqui. */
    public function index(Request $request): JsonResponse
    {
        $query = Profissional::with('comunidade:id,nome,slug')
            ->where('mototaxi', true)
            ->where('ativo', true);

        if ($request->filled('comunidade_id')) {
            $query->where('comunidade_id', $request->comunidade_id);
        }

        $lista = $query->get()
            ->sortBy([fn ($a, $b) => $b->online <=> $a->online, ['nome', 'asc']])
            ->values()
            ->map(fn (Profissional $p) => [
                'id'         => $p->id,
                'nome'       => $p->nome,
                'whatsapp'   => $p->whatsapp,
                'horario'    => $p->horario,
                'foto_url'   => $p->foto_url,
                'online'     => $p->online,
                'visto_em'   => $p->online_em?->toIso8601String(),
                'comunidade' => $p->comunidade?->nome,
            ]);

        return response()->json([
            'data'    => $lista,
            'online'  => $lista->where('online', true)->count(),
            'agora'   => now()->toIso8601String(),
        ]);
    }

    /** Cadastro pelo link do assistente: nome, telefone e horário (opcional). Entra "em análise". */
    public function cadastro(Request $request): JsonResponse
    {
        // Isca anti-robô: pessoas não veem este campo.
        if ($request->filled('apelido')) {
            return response()->json(['data' => ['nome' => 'Mototaxista']], 201);
        }

        $request->merge(['whatsapp' => CadastroProfissional::normalizarWhatsapp($request->input('whatsapp'))]);

        $data = $request->validate([
            'nome'          => ['required', 'string', 'min:2', 'max:100'],
            'whatsapp'      => ['required', 'string'],
            'horario'       => ['nullable', 'string', 'max:80'],
            'comunidade_id' => ['nullable', 'exists:comunidades,id'],
        ], [
            'nome.required'     => 'Diga o seu nome.',
            'whatsapp.required' => 'Confira o WhatsApp: use o DDD e o número (ex.: 21 99999-8888).',
        ]);

        $nome = trim(strip_tags($data['nome']));
        $horario = isset($data['horario']) ? (trim(strip_tags($data['horario'])) ?: null) : null;
        $whatsapp = '55' . $data['whatsapp'];

        if (Profissional::withTrashed()->where('mototaxi', true)->whereIn('whatsapp', [$whatsapp, $data['whatsapp']])->exists()) {
            // Não devolve o token: quem só sabe o número não pode assumir o painel de outra pessoa.
            return response()->json(['data' => ['nome' => explode(' ', $nome)[0], 'ja_cadastrado' => true]], 200);
        }

        $p = Profissional::create([
            'comunidade_id' => $data['comunidade_id'] ?? Comunidade::query()->orderBy('id')->value('id'),
            'nome'          => $nome,
            'categoria'     => 'Transporte',
            'cargo'         => 'Mototáxi',
            'bio'           => 'Mototáxi da comunidade. Chame no WhatsApp.',
            'whatsapp'      => $whatsapp,
            'tags'          => ['mototaxi', 'moto taxi', 'transporte'],
            'mototaxi'      => true,
            'horario'       => $horario,
            'ativo'         => false, // o admin aprova antes de aparecer na lista
            'painel_token'  => Str::random(40),
        ]);

        return response()->json(['data' => [
            'nome'  => explode(' ', $nome)[0],
            'token' => $p->painel_token,
        ]], 201);
    }

    public function painel(string $token): JsonResponse
    {
        return response()->json(['data' => $this->resumo($this->achar($token))]);
    }

    /** "Estou rodando": renova o online e guarda a posição, se o celular deu. */
    public function ping(Request $request, string $token): JsonResponse
    {
        $p = $this->achar($token);

        $data = $request->validate([
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $p->forceFill(['online_em' => now()] + (isset($data['lat'], $data['lng']) ? ['lat' => $data['lat'], 'lng' => $data['lng']] : []))->save();

        return response()->json(['data' => $this->resumo($p)]);
    }

    public function offline(string $token): JsonResponse
    {
        $p = $this->achar($token);
        $p->forceFill(['online_em' => null])->save();

        return response()->json(['data' => $this->resumo($p)]);
    }

    private function achar(string $token): Profissional
    {
        if (strlen($token) < 20) {
            throw ValidationException::withMessages(['token' => ['Link inválido.']]);
        }

        return Profissional::where('mototaxi', true)->where('painel_token', $token)->firstOrFail();
    }

    private function resumo(Profissional $p): array
    {
        return [
            'nome'     => $p->nome,
            'horario'  => $p->horario,
            'aprovado' => (bool) $p->ativo,
            'online'   => $p->online,
        ];
    }
}

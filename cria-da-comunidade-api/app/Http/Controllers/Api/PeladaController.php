<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelada;
use App\Models\PeladaJogador;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PeladaController extends Controller
{
    private const TIPOS = ['quadra', 'society', 'campo', 'racha'];

    public function index(Request $request): JsonResponse
    {
        $query = Pelada::with('organizador:id,name')
            ->withCount('jogadores')
            ->where('ativo', true)
            ->latest();

        if ($request->filled('comunidade_id')) {
            $query->where('comunidade_id', $request->comunidade_id);
        }

        $peladas = $query->paginate($request->integer('per_page', 30));

        return response()->json([
            'data' => $peladas->items(),
            'total' => $peladas->total(),
        ]);
    }

    public function show(Pelada $pelada): JsonResponse
    {
        return response()->json($this->payload($pelada));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nome' => 'required|string|max:120',
            'tipo' => ['required', Rule::in(self::TIPOS)],
            'jogadores_linha' => 'required|integer|min:2|max:10',
            'goleiros_por_time' => 'required|integer|in:0,1',
            'tempo_jogo_min' => 'required|integer|min:3|max:120',
            'local' => 'nullable|string|max:160',
            'quando' => 'nullable|string|max:120',
            'comunidade_id' => 'nullable|exists:comunidades,id',
        ]);

        $user = $request->user();
        $data['comunidade_id'] = $data['comunidade_id'] ?? $user->comunidade_id;
        $data['organizador_id'] = $user->id;

        $pelada = Pelada::create($data);

        return response()->json($this->payload($pelada), 201);
    }

    public function update(Request $request, Pelada $pelada): JsonResponse
    {
        $this->autorizarOrganizador($request, $pelada);

        $data = $request->validate([
            'nome' => 'sometimes|string|max:120',
            'tipo' => ['sometimes', Rule::in(self::TIPOS)],
            'jogadores_linha' => 'sometimes|integer|min:2|max:10',
            'goleiros_por_time' => 'sometimes|integer|in:0,1',
            'tempo_jogo_min' => 'sometimes|integer|min:3|max:120',
            'local' => 'nullable|string|max:160',
            'quando' => 'nullable|string|max:120',
            'ativo' => 'sometimes|boolean',
        ]);

        $pelada->update($data);

        return response()->json($this->payload($pelada));
    }

    public function destroy(Request $request, Pelada $pelada): JsonResponse
    {
        $this->autorizarOrganizador($request, $pelada);
        $pelada->delete();

        return response()->json(null, 204);
    }

    public function entrar(Request $request, Pelada $pelada): JsonResponse
    {
        $data = $request->validate(['goleiro' => 'sometimes|boolean']);
        $goleiro = (bool) ($data['goleiro'] ?? false);

        if ($goleiro && $pelada->goleiros_por_time === 0) {
            return response()->json(['message' => 'Esta pelada não tem vaga de goleiro.'], 422);
        }

        return DB::transaction(function () use ($request, $pelada, $goleiro) {
            Pelada::whereKey($pelada->id)->lockForUpdate()->first();

            $jaEsta = PeladaJogador::where('pelada_id', $pelada->id)
                ->where('user_id', $request->user()->id)
                ->exists();

            if ($jaEsta) {
                return response()->json(['message' => 'Você já está na lista.'], 422);
            }

            PeladaJogador::create([
                'pelada_id' => $pelada->id,
                'user_id' => $request->user()->id,
                'goleiro' => $goleiro,
                'posicao' => $pelada->proximaPosicao(),
            ]);

            return response()->json($this->payload($pelada), 201);
        });
    }

    public function sair(Request $request, Pelada $pelada): JsonResponse
    {
        PeladaJogador::where('pelada_id', $pelada->id)
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->json($this->payload($pelada));
    }

    public function voltarFila(Request $request, Pelada $pelada): JsonResponse
    {
        return DB::transaction(function () use ($request, $pelada) {
            Pelada::whereKey($pelada->id)->lockForUpdate()->first();

            $meu = PeladaJogador::where('pelada_id', $pelada->id)
                ->where('user_id', $request->user()->id)
                ->first();

            if (! $meu) {
                return response()->json(['message' => 'Você não está na lista.'], 422);
            }

            $pelada->moverParaFim([$meu->id]);

            return response()->json($this->payload($pelada));
        });
    }

    public function convidado(Request $request, Pelada $pelada): JsonResponse
    {
        $this->autorizarOrganizador($request, $pelada);

        $data = $request->validate([
            'nome' => 'required|string|max:80',
            'goleiro' => 'sometimes|boolean',
        ]);
        $goleiro = (bool) ($data['goleiro'] ?? false);

        if ($goleiro && $pelada->goleiros_por_time === 0) {
            return response()->json(['message' => 'Esta pelada não tem vaga de goleiro.'], 422);
        }

        return DB::transaction(function () use ($pelada, $data, $goleiro) {
            Pelada::whereKey($pelada->id)->lockForUpdate()->first();

            PeladaJogador::create([
                'pelada_id' => $pelada->id,
                'nome_convidado' => $data['nome'],
                'goleiro' => $goleiro,
                'posicao' => $pelada->proximaPosicao(),
            ]);

            return response()->json($this->payload($pelada), 201);
        });
    }

    public function removerJogador(Request $request, Pelada $pelada, PeladaJogador $jogador): JsonResponse
    {
        $this->autorizarOrganizador($request, $pelada);
        abort_unless($jogador->pelada_id === $pelada->id, 404);

        $jogador->delete();

        return response()->json($this->payload($pelada));
    }

    public function rodizio(Request $request, Pelada $pelada): JsonResponse
    {
        $this->autorizarOrganizador($request, $pelada);
        $data = $request->validate(['time' => 'required|integer|min:0']);

        return DB::transaction(function () use ($pelada, $data) {
            Pelada::whereKey($pelada->id)->lockForUpdate()->first();

            if (! isset($pelada->montarTimes()[$data['time']])) {
                return response()->json(['message' => 'Time inexistente.'], 422);
            }

            $pelada->rodizio($data['time']);

            return response()->json($this->payload($pelada));
        });
    }

    public function zerar(Request $request, Pelada $pelada): JsonResponse
    {
        $this->autorizarOrganizador($request, $pelada);

        PeladaJogador::where('pelada_id', $pelada->id)->delete();

        return response()->json($this->payload($pelada));
    }

    private function payload(Pelada $pelada): array
    {
        $pelada->unsetRelation('jogadores');
        $pelada->load(['organizador:id,name', 'jogadores.user:id,name']);

        return [
            'id' => $pelada->id,
            'comunidade_id' => $pelada->comunidade_id,
            'organizador' => $pelada->organizador,
            'nome' => $pelada->nome,
            'tipo' => $pelada->tipo,
            'jogadores_linha' => $pelada->jogadores_linha,
            'goleiros_por_time' => $pelada->goleiros_por_time,
            'tempo_jogo_min' => $pelada->tempo_jogo_min,
            'local' => $pelada->local,
            'quando' => $pelada->quando,
            'ativo' => $pelada->ativo,
            'total_jogadores' => $pelada->jogadores->count(),
            'times' => $pelada->montarTimes(),
        ];
    }

    private function autorizarOrganizador(Request $request, Pelada $pelada): void
    {
        $user = $request->user();

        abort_unless(
            $user->id === $pelada->organizador_id
                || $user->hasRole(['super_admin', 'admin', 'moderador']),
            403,
            'Só o organizador pode fazer isso.'
        );
    }
}

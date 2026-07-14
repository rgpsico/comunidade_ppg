<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Avaliacao;
use App\Models\Evento;
use App\Models\Loja;
use App\Models\Profissional;
use App\Models\Projeto;
use App\Models\Vaga;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvaliacaoController extends Controller
{
    /** Mapa tipo-da-rota => model — adicione novos módulos aqui */
    private array $tipoMap = [
        'profissionais' => Profissional::class,
        'eventos'       => Evento::class,
        'projetos'      => Projeto::class,
        'lojas'         => Loja::class,
        'vagas'         => Vaga::class,
    ];

    private function resolveModel(string $tipo, int $id): object
    {
        abort_unless(isset($this->tipoMap[$tipo]), 404, 'Módulo não suportado');
        return $this->tipoMap[$tipo]::findOrFail($id);
    }

    /** GET /{tipo}/{id}/avaliacoes */
    public function index(Request $request, string $tipo, int $id): JsonResponse
    {
        $model  = $this->resolveModel($tipo, $id);
        $userId = $request->user()?->id;
        $type   = get_class($model);

        $avaliacoes = Avaliacao::with('user:id,name,avatar')
            ->where('avaliavel_type', $type)
            ->where('avaliavel_id', $id)
            ->latest()
            ->paginate(20);

        $minha = $userId
            ? Avaliacao::where(['avaliavel_type' => $type, 'avaliavel_id' => $id, 'user_id' => $userId])->first()
            : null;

        $media = (float) Avaliacao::where('avaliavel_type', $type)->where('avaliavel_id', $id)->avg('nota');

        return response()->json([
            'data'            => $avaliacoes->map(fn ($a) => $this->fmt($a, $userId)),
            'media'           => round($media, 1),
            'total'           => $avaliacoes->total(),
            'minha_avaliacao' => $minha ? $this->fmt($minha->load('user:id,name,avatar'), $userId) : null,
        ]);
    }

    /** POST /{tipo}/{id}/avaliacoes  (auth) */
    public function store(Request $request, string $tipo, int $id): JsonResponse
    {
        $model = $this->resolveModel($tipo, $id);

        $data = $request->validate([
            'nota'  => 'required|integer|min:1|max:5',
            'texto' => 'nullable|string|max:1000',
        ]);

        $avaliacao = Avaliacao::updateOrCreate(
            [
                'avaliavel_type' => get_class($model),
                'avaliavel_id'   => $id,
                'user_id'        => $request->user()->id,
            ],
            $data
        );

        if (method_exists($model, 'recalcularEstrelas')) {
            $model->recalcularEstrelas();
        }

        return response()->json($this->fmt($avaliacao->load('user:id,name,avatar'), $request->user()->id), 201);
    }

    /** DELETE /avaliacoes/{avaliacao}  (auth, dono) */
    public function destroy(Request $request, Avaliacao $avaliacao): JsonResponse
    {
        abort_unless($avaliacao->user_id === $request->user()->id, 403, 'Sem permissão');

        $model = $avaliacao->avaliavel;
        $avaliacao->delete();

        if ($model && method_exists($model, 'recalcularEstrelas')) {
            $model->recalcularEstrelas();
        }

        return response()->json(['ok' => true]);
    }

    private function fmt(Avaliacao $a, ?int $userId): array
    {
        return [
            'id'         => $a->id,
            'nota'       => $a->nota,
            'texto'      => $a->texto,
            'user'       => ['id' => $a->user->id, 'name' => $a->user->name, 'avatar' => $a->user->avatar ?? null],
            'created_at' => $a->created_at->toIso8601String(),
            'is_mine'    => $userId === $a->user_id,
        ];
    }
}

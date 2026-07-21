<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artigo;
use App\Models\Comentario;
use App\Models\Evento;
use App\Models\Loja;
use App\Models\Profissional;
use App\Models\Projeto;
use App\Models\Vaga;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComentarioController extends Controller
{
    private array $tipoMap = [
        'profissionais' => Profissional::class,
        'eventos'       => Evento::class,
        'projetos'      => Projeto::class,
        'lojas'         => Loja::class,
        'vagas'         => Vaga::class,
        'artigos'       => Artigo::class,
    ];

    private function resolveModel(string $tipo, int $id): object
    {
        abort_unless(isset($this->tipoMap[$tipo]), 404, 'Módulo não suportado');
        return $this->tipoMap[$tipo]::findOrFail($id);
    }

    /** GET /{tipo}/{id}/comentarios */
    public function index(Request $request, string $tipo, int $id): JsonResponse
    {
        $model  = $this->resolveModel($tipo, $id);
        $userId = $request->user()?->id;
        $type   = get_class($model);

        $comentarios = Comentario::with(['user:id,name,avatar', 'respostas.user:id,name,avatar'])
            ->where('comentavel_type', $type)
            ->where('comentavel_id', $id)
            ->whereNull('parent_id')
            ->latest()
            ->paginate(30);

        return response()->json([
            'data'  => $comentarios->map(fn ($c) => $this->fmt($c, $userId)),
            'total' => $comentarios->total(),
        ]);
    }

    /** POST /{tipo}/{id}/comentarios  (auth) */
    public function store(Request $request, string $tipo, int $id): JsonResponse
    {
        $model = $this->resolveModel($tipo, $id);

        $data = $request->validate([
            'corpo'     => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comentarios,id',
        ]);

        $comentario = Comentario::create([
            'comentavel_type' => get_class($model),
            'comentavel_id'   => $id,
            'user_id'         => $request->user()->id,
            'parent_id'       => $data['parent_id'] ?? null,
            'corpo'           => $data['corpo'],
        ]);

        return response()->json(
            $this->fmt($comentario->load('user:id,name,avatar'), $request->user()->id),
            201
        );
    }

    /** DELETE /comentarios/{comentario}  (auth, dono) */
    public function destroy(Request $request, Comentario $comentario): JsonResponse
    {
        abort_unless($comentario->user_id === $request->user()->id, 403, 'Sem permissão');
        $comentario->delete();
        return response()->json(['ok' => true]);
    }

    private function fmt(Comentario $c, ?int $userId): array
    {
        return [
            'id'         => $c->id,
            'corpo'      => $c->corpo,
            'user'       => ['id' => $c->user->id, 'name' => $c->user->name, 'avatar' => $c->user->avatar ?? null],
            'respostas'  => $c->respostas
                ? $c->respostas->map(fn ($r) => $this->fmt($r, $userId))->values()->toArray()
                : [],
            'created_at' => $c->created_at->toIso8601String(),
            'is_mine'    => $userId === $c->user_id,
        ];
    }
}

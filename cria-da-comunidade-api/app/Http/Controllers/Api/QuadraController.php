<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quadra;
use App\Models\QuadraHorario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuadraController extends Controller
{
    /** Quadras ativas com seus times ativos e a grade semanal (segunda → domingo). */
    public function index(Request $request): JsonResponse
    {
        $query = Quadra::query()
            ->where('ativo', true)
            ->with(['times' => fn ($q) => $q->where('ativo', true)->with('horarios')])
            ->orderBy('nome');

        if ($request->filled('comunidade_id')) {
            $query->where('comunidade_id', $request->comunidade_id);
        }

        $data = $query->get()->map(function (Quadra $quadra) {
            $times = $quadra->times->filter(fn ($t) => $t->horarios->isNotEmpty())->values();

            $semana = collect([1, 2, 3, 4, 5, 6, 0])->map(function (int $dia) use ($times) {
                $slots = $times->flatMap(fn ($t) => $t->horarios
                    ->where('dia_semana', $dia)
                    ->map(fn (QuadraHorario $h) => [
                        'time_id'     => $t->id,
                        'time_nome'   => $t->nome,
                        'hora_inicio' => QuadraHorario::hm($h->hora_inicio),
                        'hora_fim'    => QuadraHorario::hm($h->hora_fim),
                    ]))
                    ->sortBy('hora_inicio')
                    ->values();

                return ['dia' => $dia, 'nome' => QuadraHorario::DIAS[$dia], 'slots' => $slots];
            })->filter(fn ($d) => $d['slots']->isNotEmpty())->values();

            return [
                'id'        => $quadra->id,
                'nome'      => $quadra->nome,
                'endereco'  => $quadra->endereco,
                'descricao' => $quadra->descricao,
                'times'     => $times->map(fn ($t) => [
                    'id'        => $t->id,
                    'nome'      => $t->nome,
                    'descricao' => $t->descricao,
                    'regras'    => $t->regras,
                    'horarios'  => $t->horarios->map(fn (QuadraHorario $h) => [
                        'dia_semana'  => $h->dia_semana,
                        'hora_inicio' => QuadraHorario::hm($h->hora_inicio),
                        'hora_fim'    => QuadraHorario::hm($h->hora_fim),
                    ])->values(),
                ])->values(),
                'semana'    => $semana,
            ];
        });

        return response()->json(['data' => $data]);
    }
}

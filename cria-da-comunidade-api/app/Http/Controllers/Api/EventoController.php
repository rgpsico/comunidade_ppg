<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\Rsvp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Evento::with(['organizador:id,name', 'comunidade:id,nome'])
            ->where('ativo', true);

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }
        if ($request->filled('comunidade_id')) {
            $query->where('comunidade_id', $request->comunidade_id);
        }
        if ($request->filled('data_from')) {
            $query->where('data_hora', '>=', $request->data_from);
        }
        if ($request->boolean('destaque')) {
            $query->where('destaque', true);
        }

        $query->orderBy('data_hora', 'asc');

        return response()->json($query->paginate(12));
    }

    public function show(Evento $evento): JsonResponse
    {
        return response()->json(
            $evento->load(['organizador:id,name', 'comunidade:id,nome', 'rsvps'])
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'titulo'       => 'required|string|max:200',
            'descricao'    => 'nullable|string',
            'categoria'    => 'required|string',
            'data_hora'    => 'required|date',
            'local'        => 'nullable|string|max:200',
            'gratuito'     => 'boolean',
            'preco'        => 'nullable|numeric|min:0',
            'idade_minima' => 'integer|min:0',
            'duracao'      => 'nullable|string',
            'comunidade_id'=> 'nullable|exists:comunidades,id',
            'cor1'         => 'nullable|string|max:7',
            'cor2'         => 'nullable|string|max:7',
        ]);

        $evento = Evento::create([
            ...$data,
            'organizador_id' => auth()->id(),
        ]);

        return response()->json($evento, 201);
    }

    public function rsvp(Request $request, Evento $evento): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:going,interested,not_going',
        ]);

        $rsvp = Rsvp::updateOrCreate(
            ['evento_id' => $evento->id, 'user_id' => auth()->id()],
            ['status' => $request->status]
        );

        // Atualizar contadores
        $evento->update([
            'confirmados'  => $evento->rsvps()->where('status', 'going')->count(),
            'interessados' => $evento->rsvps()->where('status', 'interested')->count(),
        ]);

        return response()->json($rsvp);
    }

    public function destroy(Evento $evento): JsonResponse
    {
        $this->authorize('delete', $evento);
        $evento->delete();
        return response()->json(null, 204);
    }
}

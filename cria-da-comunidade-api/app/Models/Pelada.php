<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelada extends Model
{
    protected $fillable = [
        'comunidade_id', 'organizador_id', 'nome', 'tipo',
        'jogadores_linha', 'goleiros_por_time', 'tempo_jogo_min',
        'local', 'quando', 'ativo',
    ];

    protected $casts = [
        'jogadores_linha' => 'integer',
        'goleiros_por_time' => 'integer',
        'tempo_jogo_min' => 'integer',
        'ativo' => 'boolean',
    ];

    public function organizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizador_id');
    }

    public function comunidade(): BelongsTo
    {
        return $this->belongsTo(Comunidade::class);
    }

    public function jogadores(): HasMany
    {
        return $this->hasMany(PeladaJogador::class)->orderBy('posicao');
    }

    /**
     * Times derivados da fila: linha em blocos de N, goleiros em blocos de G.
     * Times 0 e 1 estão em quadra; os demais esperam na fila.
     *
     * @return array<int, array<string, mixed>>
     */
    public function montarTimes(): array
    {
        $jogadores = $this->relationLoaded('jogadores')
            ? $this->jogadores
            : $this->jogadores()->with('user:id,name')->get();

        $linha = $jogadores->where('goleiro', false)->values();
        $goleiros = $jogadores->where('goleiro', true)->values();

        $n = max(1, $this->jogadores_linha);
        $g = $this->goleiros_por_time;

        $qtdTimes = max(
            (int) ceil($linha->count() / $n),
            $g > 0 ? (int) ceil($goleiros->count() / $g) : 0,
        );

        $times = [];
        for ($k = 0; $k < $qtdTimes; $k++) {
            $doTime = $linha->slice($k * $n, $n)->values();
            $golsDoTime = $g > 0 ? $goleiros->slice($k * $g, $g)->values() : collect();

            $times[] = [
                'indice' => $k,
                'status' => $k < 2 ? 'quadra' : 'fila',
                'ordem_fila' => $k < 2 ? null : $k - 1,
                'linha' => $doTime->map->toFila()->all(),
                'goleiro' => $golsDoTime->first()?->toFila(),
                'vagas_linha' => $n - $doTime->count(),
                'vaga_goleiro' => $g > 0 && $golsDoTime->isEmpty(),
                'completo' => $doTime->count() === $n && ($g === 0 || $golsDoTime->count() === $g),
            ];
        }

        return $times;
    }

    /**
     * Move os jogadores do time informado para o fim da fila, preservando a ordem.
     * Deve ser chamado dentro de uma transação com a pelada travada.
     */
    public function rodizio(int $indiceTime): void
    {
        $times = $this->montarTimes();
        if (! isset($times[$indiceTime])) {
            return;
        }

        $ids = collect($times[$indiceTime]['linha'])->pluck('id')->all();
        if ($times[$indiceTime]['goleiro']) {
            $ids[] = $times[$indiceTime]['goleiro']['id'];
        }

        $this->moverParaFim($ids);
    }

    /** @param  array<int, int>  $ids */
    public function moverParaFim(array $ids): void
    {
        $proxima = (int) PeladaJogador::where('pelada_id', $this->id)->max('posicao');

        $ordenados = PeladaJogador::whereIn('id', $ids)
            ->where('pelada_id', $this->id)
            ->orderBy('posicao')
            ->get();

        foreach ($ordenados as $jogador) {
            $jogador->update(['posicao' => ++$proxima]);
        }
    }

    public function proximaPosicao(): int
    {
        return ((int) PeladaJogador::where('pelada_id', $this->id)->max('posicao')) + 1;
    }
}

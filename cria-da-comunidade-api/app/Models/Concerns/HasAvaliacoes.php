<?php

namespace App\Models\Concerns;

use App\Models\Avaliacao;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasAvaliacoes
{
    public function avaliacoes(): MorphMany
    {
        return $this->morphMany(Avaliacao::class, 'avaliavel')->latest();
    }

    /** Recalcula estrelas e total_avaliacoes a partir das avaliações reais */
    public function recalcularEstrelas(): void
    {
        $media = $this->avaliacoes()->avg('nota') ?? 0;
        $total = $this->avaliacoes()->count();

        $this->update([
            'estrelas'         => round((float) $media, 1),
            'total_avaliacoes' => $total,
        ]);
    }
}

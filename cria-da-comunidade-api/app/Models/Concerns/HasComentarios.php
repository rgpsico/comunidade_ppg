<?php

namespace App\Models\Concerns;

use App\Models\Comentario;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasComentarios
{
    /** Retorna apenas comentários raiz (sem parent) */
    public function comentarios(): MorphMany
    {
        return $this->morphMany(Comentario::class, 'comentavel')
            ->whereNull('parent_id')
            ->latest();
    }
}

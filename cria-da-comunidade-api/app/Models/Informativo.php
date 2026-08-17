<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Informativo extends Model
{
    protected $fillable = [
        'comunidade_id', 'titulo', 'fonte', 'data_ocorrencia',
        'corpo', 'urgente', 'publicado',
    ];

    protected function casts(): array
    {
        return [
            'data_ocorrencia' => 'date',
            'urgente'         => 'boolean',
            'publicado'       => 'boolean',
        ];
    }

    public function comunidade(): BelongsTo
    {
        return $this->belongsTo(Comunidade::class);
    }
}

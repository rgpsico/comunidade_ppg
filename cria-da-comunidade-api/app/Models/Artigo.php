<?php

namespace App\Models;

use App\Models\Concerns\HasAvaliacoes;
use App\Models\Concerns\HasComentarios;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Artigo extends Model
{
    use HasAvaliacoes, HasComentarios;

    protected $fillable = [
        'comunidade_id', 'titulo', 'slug', 'resumo', 'corpo',
        'imagem_capa_url', 'categoria', 'autor', 'publicado', 'publicado_em',
    ];

    protected function casts(): array
    {
        return [
            'publicado'     => 'boolean',
            'publicado_em'  => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function comunidade(): BelongsTo
    {
        return $this->belongsTo(Comunidade::class);
    }
}

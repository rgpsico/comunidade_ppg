<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedPost extends Model
{
    protected $fillable = [
        'comunidade_id', 'autor', 'legenda', 'imagem_url',
        'cor1', 'cor2', 'tamanho', 'publicado',
    ];

    protected function casts(): array
    {
        return ['publicado' => 'boolean'];
    }

    public function comunidade(): BelongsTo
    {
        return $this->belongsTo(Comunidade::class);
    }
}

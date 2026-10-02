<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quadra extends Model
{
    protected $fillable = ['comunidade_id', 'nome', 'endereco', 'descricao', 'ativo'];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function comunidade(): BelongsTo
    {
        return $this->belongsTo(Comunidade::class);
    }

    public function times(): HasMany
    {
        return $this->hasMany(QuadraTime::class)->orderBy('nome');
    }
}

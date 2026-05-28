<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Apoio extends Model
{
    protected $fillable = [
        'projeto_id', 'user_id', 'nome_apoiador', 'valor', 'forma_pagamento', 'status',
    ];

    protected function casts(): array
    {
        return ['valor' => 'float'];
    }

    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

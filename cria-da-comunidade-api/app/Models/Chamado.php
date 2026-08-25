<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chamado extends Model
{
    protected $fillable = [
        'comunidade_id', 'user_id', 'profissional_id',
        'tipo', 'titulo', 'descricao', 'categoria', 'fotos',
        'local', 'estimativa_valor', 'valor_acordado',
        'urgencia', 'status', 'aceito_em', 'resolvido_em',
    ];

    protected $casts = [
        'fotos' => 'array',
        'aceito_em' => 'datetime',
        'resolvido_em' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function profissional(): BelongsTo
    {
        return $this->belongsTo(Profissional::class);
    }

    public function comunidade(): BelongsTo
    {
        return $this->belongsTo(Comunidade::class);
    }

    public function doacoes(): HasMany
    {
        return $this->hasMany(ChamadoDoacao::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** "Time" aqui é o titular do horário: pode ser um time ou uma pessoa. */
class QuadraTime extends Model
{
    protected $fillable = ['quadra_id', 'nome', 'descricao', 'regras', 'ativo'];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function quadra(): BelongsTo
    {
        return $this->belongsTo(Quadra::class);
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(QuadraHorario::class)->orderBy('dia_semana')->orderBy('hora_inicio');
    }

    public function resumoHorarios(): string
    {
        return $this->horarios
            ->map(fn (QuadraHorario $h) => $h->rotulo())
            ->implode(', ');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeladaJogador extends Model
{
    protected $table = 'pelada_jogadores';

    protected $fillable = ['pelada_id', 'user_id', 'nome_convidado', 'goleiro', 'posicao'];

    protected $casts = [
        'goleiro' => 'boolean',
    ];

    public function pelada(): BelongsTo
    {
        return $this->belongsTo(Pelada::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function nomeExibicao(): string
    {
        return $this->user?->name ?? $this->nome_convidado ?? 'Jogador';
    }

    public function toFila(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'nome' => $this->nomeExibicao(),
            'convidado' => $this->user_id === null,
            'goleiro' => $this->goleiro,
            'posicao' => $this->posicao,
        ];
    }
}

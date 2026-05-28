<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comunidade extends Model
{
    protected $fillable = [
        'nome', 'cidade', 'estado', 'cep', 'lat', 'lng',
        'descricao', 'imagem', 'ativa',
    ];

    protected function casts(): array
    {
        return ['ativa' => 'boolean', 'lat' => 'float', 'lng' => 'float'];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function profissionais(): HasMany
    {
        return $this->hasMany(Profissional::class);
    }

    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class);
    }

    public function projetos(): HasMany
    {
        return $this->hasMany(Projeto::class);
    }

    public function vagas(): HasMany
    {
        return $this->hasMany(Vaga::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Evento extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organizador_id', 'comunidade_id', 'titulo', 'descricao', 'imagem_capa', 'galeria',
        'categoria', 'data_hora', 'local', 'lat', 'lng', 'gratuito', 'preco', 'idade_minima',
        'duracao', 'confirmados', 'interessados', 'cor1', 'cor2', 'destaque', 'ativo',
    ];

    protected $appends = ['imagem_capa_url', 'galeria_urls'];

    protected function casts(): array
    {
        return [
            'data_hora' => 'datetime',
            'gratuito'  => 'boolean',
            'destaque'  => 'boolean',
            'ativo'     => 'boolean',
            'preco'     => 'float',
            'lat'       => 'float',
            'lng'       => 'float',
            'galeria'   => 'array',
        ];
    }

    public function getImagemCapaUrlAttribute(): ?string
    {
        return $this->imagem_capa ? Storage::disk('public')->url($this->imagem_capa) : null;
    }

    public function getGaleriaUrlsAttribute(): array
    {
        if (!$this->galeria) return [];
        return collect($this->galeria)
            ->map(fn ($p) => Storage::disk('public')->url($p))
            ->values()
            ->toArray();
    }

    public function organizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizador_id');
    }

    public function comunidade(): BelongsTo
    {
        return $this->belongsTo(Comunidade::class);
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(Rsvp::class);
    }

    public function getDayAttribute(): string
    {
        return $this->data_hora->format('d');
    }

    public function getMonthAttribute(): string
    {
        return strtoupper($this->data_hora->locale('pt_BR')->isoFormat('MMM'));
    }
}

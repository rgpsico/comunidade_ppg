<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Projeto extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'responsavel_id', 'comunidade_id', 'nome', 'descricao', 'icone',
        'imagem_capa', 'galeria',
        'causa', 'cor', 'impacto_valor', 'impacto_label', 'progresso',
        'arrecadado', 'meta', 'cta_label', 'anos_atuando', 'ativo',
    ];

    protected $appends = ['imagem_capa_url', 'galeria_urls'];

    protected function casts(): array
    {
        return [
            'ativo'      => 'boolean',
            'arrecadado' => 'float',
            'meta'       => 'float',
            'progresso'  => 'integer',
            'galeria'    => 'array',
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

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    public function comunidade(): BelongsTo
    {
        return $this->belongsTo(Comunidade::class);
    }

    public function apoios(): HasMany
    {
        return $this->hasMany(Apoio::class);
    }

    public function getProgressoPercentualAttribute(): int
    {
        if (!$this->meta || $this->meta <= 0) return 0;
        return min(100, (int) round(($this->arrecadado / $this->meta) * 100));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Profissional extends Model
{
    use SoftDeletes;

    protected $table = 'profissionais';

    protected $fillable = [
        'user_id', 'comunidade_id', 'nome', 'categoria', 'cargo', 'bio',
        'foto', 'galeria',
        'estrelas', 'total_avaliacoes', 'total_atendimentos', 'preco_a_partir',
        'whatsapp', 'cor1', 'cor2', 'tags', 'tempo_resposta', 'verificado', 'ativo',
    ];

    protected $appends = ['foto_url', 'galeria_urls'];

    protected function casts(): array
    {
        return [
            'tags'           => 'array',
            'galeria'        => 'array',
            'verificado'     => 'boolean',
            'ativo'          => 'boolean',
            'estrelas'       => 'float',
            'preco_a_partir' => 'float',
        ];
    }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? Storage::disk('public')->url($this->foto) : null;
    }

    public function getGaleriaUrlsAttribute(): array
    {
        if (!$this->galeria) return [];
        return collect($this->galeria)
            ->map(fn ($p) => Storage::disk('public')->url($p))
            ->values()
            ->toArray();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comunidade(): BelongsTo
    {
        return $this->belongsTo(Comunidade::class);
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->nome);
        return strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
    }
}

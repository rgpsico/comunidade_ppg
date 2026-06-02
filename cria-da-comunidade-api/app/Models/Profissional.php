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
        'whatsapp', 'cor1', 'cor2', 'tags', 'tempo_resposta',
        'verificado', 'ativo', 'plano', 'premium_expira_em',
    ];

    protected $appends = ['foto_url', 'galeria_urls', 'is_premium'];

    protected function casts(): array
    {
        return [
            'tags'                => 'array',
            'galeria'             => 'array',
            'verificado'          => 'boolean',
            'ativo'               => 'boolean',
            'estrelas'            => 'float',
            'preco_a_partir'      => 'float',
            'premium_expira_em'   => 'datetime',
        ];
    }

    /** Premium = plano premium E (sem validade OU validade futura) */
    public function getIsPremiumAttribute(): bool
    {
        return $this->plano === 'premium'
            && ($this->premium_expira_em === null || $this->premium_expira_em->isFuture());
    }

    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto) {
            return Storage::disk('public')->url($this->foto);
        }

        // Avatar gerado automaticamente pela inicial do nome
        $cor = ltrim($this->cor1 ?? '#FF5E1A', '#');
        $nome = urlencode($this->nome);
        return "https://ui-avatars.com/api/?name={$nome}&background={$cor}&color=fff&size=200&bold=true&format=png";
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

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

    protected $appends = ['fotos_urls'];

    /** Fotos do problema como URLs públicas (o banco guarda o caminho no disco "public"). */
    public function getFotosUrlsAttribute(): array
    {
        return collect($this->fotos ?? [])
            ->filter()
            ->map(fn ($p) => str_starts_with($p, 'http') ? $p : Storage::disk('public')->url($p))
            ->values()
            ->all();
    }

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

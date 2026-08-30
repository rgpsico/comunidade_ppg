<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Produto extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'loja_id', 'nome', 'descricao', 'preco', 'preco_promocional',
        'imagens', 'categoria', 'disponivel', 'destaque', 'ordem',
    ];

    protected $appends = ['imagens_urls', 'imagem_principal_url'];

    protected function casts(): array
    {
        return [
            'imagens'           => 'array',
            'disponivel'        => 'boolean',
            'destaque'          => 'boolean',
            'preco'             => 'float',
            'preco_promocional' => 'float',
        ];
    }

    public function getImagensUrlsAttribute(): array
    {
        if (!$this->imagens) return [];
        return collect($this->imagens)
            ->map(fn ($p) => str_starts_with($p, 'http') ? $p : Storage::disk('public')->url($p))
            ->values()
            ->toArray();
    }

    public function getImagemPrincipalUrlAttribute(): ?string
    {
        $urls = $this->imagens_urls;
        return $urls[0] ?? null;
    }

    public function loja(): BelongsTo
    {
        return $this->belongsTo(Loja::class);
    }
}

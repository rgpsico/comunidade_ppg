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
        'garantia', 'variacoes', 'estoque',
        'fornecedor_nome', 'fornecedor_contato', 'fornecedor_obs',
    ];

    protected $appends = ['imagens_urls', 'imagem_principal_url'];

    // Fornecedor é dado interno da revenda: fora de toda resposta pública (lista da loja, página do produto).
    // Só o dono/admin recebe, via comFornecedor().
    public const CAMPOS_FORNECEDOR = ['fornecedor_nome', 'fornecedor_contato', 'fornecedor_obs'];

    protected $hidden = self::CAMPOS_FORNECEDOR;

    public function comFornecedor(): static
    {
        return $this->makeVisible(self::CAMPOS_FORNECEDOR);
    }

    protected function casts(): array
    {
        return [
            'imagens'           => 'array',
            'disponivel'        => 'boolean',
            'destaque'          => 'boolean',
            'preco'             => 'float',
            'preco_promocional' => 'float',
            'estoque'           => 'integer',
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Loja extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'comunidade_id', 'nome', 'descricao', 'categoria',
        'logo', 'capa', 'whatsapp', 'endereco',
        'cor1', 'cor2', 'verificado', 'ativo',
    ];

    protected $appends = ['logo_url', 'capa_url'];

    protected function casts(): array
    {
        return [
            'verificado' => 'boolean',
            'ativo'      => 'boolean',
        ];
    }

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo) {
            return Storage::disk('public')->url($this->logo);
        }
        $cor  = ltrim($this->cor1 ?? '#FF5E1A', '#');
        $nome = urlencode($this->nome);
        return "https://ui-avatars.com/api/?name={$nome}&background={$cor}&color=fff&size=200&bold=true&format=png";
    }

    public function getCapaUrlAttribute(): ?string
    {
        return $this->capa ? Storage::disk('public')->url($this->capa) : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comunidade(): BelongsTo
    {
        return $this->belongsTo(Comunidade::class);
    }

    public function produtos(): HasMany
    {
        return $this->hasMany(Produto::class)->orderBy('destaque', 'desc')->orderBy('ordem');
    }
}

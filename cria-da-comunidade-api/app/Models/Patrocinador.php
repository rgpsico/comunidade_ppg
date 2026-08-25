<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Patrocinador extends Model
{
    protected $table = 'patrocinadores';

    protected $fillable = [
        'comunidade_id', 'nome', 'imagem_url', 'texto',
        'link_url', 'texto_botao', 'ativo', 'publicado_em', 'expira_em',
    ];

    protected function casts(): array
    {
        return [
            'ativo'        => 'boolean',
            'publicado_em' => 'datetime',
            'expira_em'    => 'datetime',
        ];
    }

    public function comunidade(): BelongsTo
    {
        return $this->belongsTo(Comunidade::class);
    }

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true)
            ->where(function ($q) {
                $q->whereNull('expira_em')->orWhere('expira_em', '>', now());
            });
    }

    protected static function booted(): void
    {
        // Garante apenas um patrocinador ativo por comunidade
        static::saving(function (Patrocinador $model) {
            if ($model->ativo) {
                static::where('comunidade_id', $model->comunidade_id)
                    ->where('id', '!=', $model->id ?? 0)
                    ->update(['ativo' => false]);
            }
        });
    }
}

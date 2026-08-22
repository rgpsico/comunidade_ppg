<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Configuracao extends Model
{
    protected $table = 'configuracoes';

    protected $fillable = [
        'comunidade_id', 'nome_plataforma', 'logo_url', 'favicon_url',
        'cor_primaria', 'cor_secundaria', 'cor_destaque',
        'cor_fundo', 'cor_card', 'cor_texto', 'cor_muted',
        'listagem_tipo', 'itens_por_pagina',
    ];

    public static function defaults(): array
    {
        return [
            'nome_plataforma'  => 'Cria da Comunidade',
            'logo_url'         => null,
            'favicon_url'      => null,
            'cor_primaria'     => '#FF5E1A',
            'cor_secundaria'   => '#FFD23F',
            'cor_destaque'     => '#2BD96B',
            'cor_fundo'        => '#0D0B09',
            'cor_card'         => '#1C1916',
            'cor_texto'        => '#F5F0E8',
            'cor_muted'        => '#8B847B',
            'listagem_tipo'    => 'grade',
            'itens_por_pagina' => 20,
        ];
    }

    public function comunidade(): BelongsTo
    {
        return $this->belongsTo(Comunidade::class);
    }
}

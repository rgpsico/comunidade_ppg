<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProdutoInteresse extends Model
{
    protected $fillable = ['loja_id', 'produto_id', 'nome', 'telefone', 'origem'];

    public function loja(): BelongsTo
    {
        return $this->belongsTo(Loja::class);
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }
}

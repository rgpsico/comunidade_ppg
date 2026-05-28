<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VagaRequisito extends Model
{
    protected $fillable = ['vaga_id', 'descricao', 'nivel', 'ordem'];

    public function vaga(): BelongsTo
    {
        return $this->belongsTo(Vaga::class);
    }
}

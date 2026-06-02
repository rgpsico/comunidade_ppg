<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjetoAtividade extends Model
{
    protected $table = 'projeto_atividades';

    protected $fillable = [
        'projeto_id', 'titulo', 'dias', 'horario', 'descricao', 'vagas', 'ordem',
    ];

    protected function casts(): array
    {
        return [
            'vagas' => 'integer',
            'ordem' => 'integer',
        ];
    }

    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class);
    }
}

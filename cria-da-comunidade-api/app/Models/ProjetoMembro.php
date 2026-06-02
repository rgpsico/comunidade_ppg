<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProjetoMembro extends Model
{
    protected $table = 'projeto_membros';

    protected $fillable = [
        'projeto_id', 'nome', 'cargo', 'bio', 'foto', 'cor', 'ordem',
    ];

    protected $appends = ['foto_url'];

    protected function casts(): array
    {
        return [
            'ordem' => 'integer',
        ];
    }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? Storage::disk('public')->url($this->foto) : null;
    }

    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class);
    }
}

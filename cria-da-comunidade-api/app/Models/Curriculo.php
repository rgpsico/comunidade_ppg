<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Curriculo extends Model
{
    protected $fillable = [
        'user_id', 'comunidade_id', 'nome', 'email', 'telefone',
        'area_atuacao', 'habilidades', 'experiencia', 'cidade',
        'disponibilidade', 'pdf_path', 'publicado',
    ];

    protected $appends = ['pdf_url'];

    protected function casts(): array
    {
        return [
            'habilidades' => 'array',
            'publicado'   => 'boolean',
        ];
    }

    public function getPdfUrlAttribute(): ?string
    {
        return $this->pdf_path ? Storage::disk('public')->url($this->pdf_path) : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comunidade(): BelongsTo
    {
        return $this->belongsTo(Comunidade::class);
    }
}

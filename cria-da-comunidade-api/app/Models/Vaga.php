<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Vaga extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'anunciante_id', 'comunidade_id', 'titulo', 'empresa', 'descricao',
        'local', 'tipo', 'salario', 'salario_periodo', 'logo_cor', 'logo_iniciais',
        'logo_imagem', 'whatsapp', 'email_contato', 'urgente', 'candidatos', 'ativa',
    ];

    protected $appends = ['logo_imagem_url'];

    protected function casts(): array
    {
        return [
            'urgente' => 'boolean',
            'ativa'   => 'boolean',
        ];
    }

    public function getLogoImagemUrlAttribute(): ?string
    {
        return $this->logo_imagem ? Storage::disk('public')->url($this->logo_imagem) : null;
    }

    public function anunciante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'anunciante_id');
    }

    public function comunidade(): BelongsTo
    {
        return $this->belongsTo(Comunidade::class);
    }

    public function requisitos(): HasMany
    {
        return $this->hasMany(VagaRequisito::class)->orderBy('ordem');
    }

    public function beneficios(): HasMany
    {
        return $this->hasMany(VagaBeneficio::class)->orderBy('ordem');
    }
}

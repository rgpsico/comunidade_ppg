<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Informativo extends Model
{
    protected $fillable = [
        'comunidade_id', 'titulo', 'slug', 'fonte', 'data_ocorrencia',
        'corpo', 'urgente', 'publicado',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $inf) {
            if (empty($inf->slug)) {
                $base = Str::slug($inf->titulo);
                $slug = $base;
                $i = 1;
                while (self::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $inf->slug = $slug;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return [
            'data_ocorrencia' => 'date',
            'urgente'         => 'boolean',
            'publicado'       => 'boolean',
        ];
    }

    public function comunidade(): BelongsTo
    {
        return $this->belongsTo(Comunidade::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comentario extends Model
{
    protected $table = 'comentarios';

    protected $fillable = ['comentavel_type', 'comentavel_id', 'user_id', 'parent_id', 'corpo'];

    public function comentavel(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comentario::class, 'parent_id');
    }

    public function respostas(): HasMany
    {
        return $this->hasMany(Comentario::class, 'parent_id')
            ->with('user:id,name,avatar')
            ->latest();
    }
}

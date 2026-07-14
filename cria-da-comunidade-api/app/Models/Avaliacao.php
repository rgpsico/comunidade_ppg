<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Avaliacao extends Model
{
    protected $fillable = ['avaliavel_type', 'avaliavel_id', 'user_id', 'nota', 'texto'];

    protected function casts(): array
    {
        return ['nota' => 'integer'];
    }

    public function avaliavel(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

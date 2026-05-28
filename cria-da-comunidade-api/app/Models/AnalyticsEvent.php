<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'type', 'screen', 'entity_type', 'entity_id', 'entity_name',
        'session_id', 'user_id', 'ip_hash', 'created_at',
    ];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }
}

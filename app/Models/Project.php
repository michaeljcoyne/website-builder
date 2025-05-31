<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    protected $fillable = [
        'name',
        'elements',
        'settings',
        'user_id',
        'is_published',
        'preview_url'
    ];

    protected $casts = [
        'elements' => 'array',
        'settings' => 'array',
        'is_published' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

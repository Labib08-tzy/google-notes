<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'theme',
    'ai_language',
    'ai_tone',
    'ai_response_length',
    'dashboard_layout',
    'notes_per_page',
    'default_sort'
])]
class UserSetting extends Model
{
    /**
     * Get the user that owns these settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

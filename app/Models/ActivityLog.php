<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'loggable_type',
        'loggable_id',
        'action',
        'description',
    ];

    protected $table = 'activity_logs';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

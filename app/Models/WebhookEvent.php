<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    protected $fillable = [
        'event_type',
        'event_id',
        'payload',
        'status',
        'error_message',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'json',
        'processed_at' => 'datetime',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    protected $fillable = [
        'user_id',
        'chef_id',
        'event_date',
        'event_time',
        'guest_count',
        'budget_range_min',
        'budget_range_max',
        'cuisine_preferences',
        'details',
        'status',
    ];

    protected $casts = [
        'event_date' => 'date',
        'cuisine_preferences' => 'array',
        'budget_range_min' => 'decimal:2',
        'budget_range_max' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chef(): BelongsTo
    {
        return $this->belongsTo(User::class, 'chef_id');
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChefProfile extends Model
{
    protected $fillable = [
        'user_id',
        'bio',
        'specialties',
        'experience_years',
        'location',
        'avatar_url',
    ];

    protected $casts = [
        'specialties' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

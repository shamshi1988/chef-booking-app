<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceRequest extends Model
{
    public const TIME_SLOTS = [
        'morning' => 'Morning (8 AM – 12 PM)',
        'afternoon' => 'Afternoon (12 PM – 4 PM)',
        'evening' => 'Evening (4 PM – 8 PM)',
        'flexible' => 'Any time',
    ];

    public const STATUSES = [
        'open' => 'Pending',
        'closed' => 'Confirmed',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    protected $fillable = [
        'user_id',
        'service_category_id',
        'provider_id',
        'preferred_date',
        'preferred_time',
        'budget_range_min',
        'budget_range_max',
        'details',
        'answers',
        'address',
        'city',
        'pincode',
        'status',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'answers' => 'array',
        'budget_range_min' => 'decimal:2',
        'budget_range_max' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    /** The booking for the assigned (accepted) professional, if any. */
    public function acceptedBooking(): ?Booking
    {
        return Booking::whereHas('proposal', fn ($q) => $q->where('service_request_id', $this->id)->where('status', 'accepted'))
            ->with(['proposal.provider', 'rating'])
            ->latest()
            ->first();
    }

    public function timeSlotLabel(): string
    {
        if (isset(self::TIME_SLOTS[$this->preferred_time])) {
            return __(self::TIME_SLOTS[$this->preferred_time]);
        }

        // Older requests stored an exact time such as "18:00:00".
        try {
            return $this->preferred_time ? Carbon::parse($this->preferred_time)->format('g:i A') : __('Any time');
        } catch (\Throwable) {
            return (string) $this->preferred_time;
        }
    }

    public function statusLabel(): string
    {
        return __(self::STATUSES[$this->status] ?? ucfirst((string) $this->status));
    }

    /**
     * The customer's answers to the service-specific questions, as label => readable value.
     */
    public function answerSummary(): array
    {
        $answers = $this->answers ?? [];
        $labels = collect($this->category?->fields() ?? [])->pluck('label', 'key');
        $summary = [];

        foreach ($answers as $key => $value) {
            if ($value === null || $value === '' || $value === []) {
                continue;
            }
            $label = $labels[$key] ?? ucwords(str_replace('_', ' ', $key));
            $summary[$label] = is_array($value) ? implode(', ', $value) : (string) $value;
        }

        return $summary;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class Booking extends Model
{
    public const STATUSES = [
        'confirmed' => 'Confirmed',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    protected $fillable = [
        'proposal_id',
        'status',
        'payment_status',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function rating(): HasOne
    {
        return $this->hasOne(Rating::class);
    }

    /** Mark the job as done; the customer is then asked to rate the professional. */
    public function markCompleted(): void
    {
        DB::transaction(function () {
            $this->update(['status' => 'completed', 'completed_at' => $this->completed_at ?? now()]);
            $this->proposal?->serviceRequest?->update(['status' => 'completed']);
        });
    }

    /**
     * Completed bookings of this customer that still need a rating.
     * Only the assigned (accepted) professional of the customer's own request qualifies.
     */
    public function scopeAwaitingRatingBy(Builder $query, int $userId): Builder
    {
        return $query->where('status', 'completed')
            ->whereDoesntHave('rating')
            ->whereHas('proposal', fn ($p) => $p->where('status', 'accepted')
                ->whereHas('serviceRequest', fn ($r) => $r->where('user_id', $userId)));
    }
}

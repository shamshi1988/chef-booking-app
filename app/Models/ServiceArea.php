<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * A region where bookings are accepted, defined by a list of PIN codes.
 *
 * Bookings are restricted to service areas only once at least one active area exists,
 * so the website keeps working normally until the admin sets them up.
 */
class ServiceArea extends Model
{
    protected $fillable = ['name', 'city', 'pincodes', 'is_active', 'notes'];

    protected $casts = [
        'pincodes' => 'array',
        'is_active' => 'boolean',
    ];

    /** Container key for the per-request cache of active areas (there are only ever a handful). */
    private const CACHE_KEY = 'service-areas.active';

    protected static function booted(): void
    {
        static::saving(function (ServiceArea $area) {
            $area->pincodes = self::normalisePincodes($area->pincodes ?? []);
            $area->city = trim((string) $area->city);
        });
        static::saved(fn () => self::flushCache());
        static::deleted(fn () => self::flushCache());
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ServiceCategory::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** Clean a list of PIN codes: digits only, 6 digits, unique, sorted. */
    public static function normalisePincodes(array|string $pincodes): array
    {
        $list = is_string($pincodes) ? preg_split('/[\s,;]+/', $pincodes) : $pincodes;

        return collect($list)
            ->map(fn ($pin) => preg_replace('/\D/', '', (string) $pin))
            ->filter(fn ($pin) => preg_match('/^[1-9][0-9]{5}$/', $pin))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public static function activeAreas(): Collection
    {
        if (! app()->bound(self::CACHE_KEY)) {
            app()->instance(self::CACHE_KEY, self::active()->with('categories:id')->get());
        }

        return app(self::CACHE_KEY);
    }

    public static function flushCache(): void
    {
        app()->forgetInstance(self::CACHE_KEY);
    }

    /** True once the admin has switched on at least one service area. */
    public static function restrictionEnabled(): bool
    {
        return self::activeAreas()->isNotEmpty();
    }

    public function coversService(?int $categoryId): bool
    {
        if (! $categoryId || $this->categories->isEmpty()) {
            return true;
        }

        return $this->categories->contains('id', $categoryId);
    }

    /** The active area that serves this PIN code (and service, if given). */
    public static function findFor(?string $pincode, ?int $categoryId = null): ?self
    {
        $pincode = preg_replace('/\D/', '', (string) $pincode);
        if ($pincode === '') {
            return null;
        }

        return self::activeAreas()->first(
            fn (self $area) => in_array($pincode, $area->pincodes ?? [], true) && $area->coversService($categoryId)
        );
    }

    /** Is the PIN code inside any active area, regardless of service? */
    public static function pincodeServed(?string $pincode): bool
    {
        return self::findFor($pincode) !== null;
    }

    /** All PIN codes customers can book from (optionally for one service). */
    public static function bookablePincodes(?int $categoryId = null): array
    {
        return self::activeAreas()
            ->filter(fn (self $area) => $area->coversService($categoryId))
            ->flatMap(fn (self $area) => $area->pincodes ?? [])
            ->unique()->sort()->values()->all();
    }

    /** Cities to show on the website: from active areas, or the config list if none are set up. */
    public static function cities(): array
    {
        $cities = self::activeAreas()->pluck('city')->filter()->unique()->sort()->values()->all();

        return $cities ?: config('site.cities', []);
    }
}

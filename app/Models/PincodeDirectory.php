<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * One post office in the India Post PIN code directory.
 * Used by the admin to pick all PIN codes of a district or locality automatically.
 */
class PincodeDirectory extends Model
{
    protected $table = 'pincode_directory';

    public $timestamps = false;

    protected $fillable = ['pincode', 'office_name', 'district', 'state'];

    public static function isLoaded(): bool
    {
        return static::query()->exists();
    }

    public static function states(): array
    {
        return static::query()->distinct()->orderBy('state')->pluck('state', 'state')->all();
    }

    public static function districts(?string $state): array
    {
        if (! $state) {
            return [];
        }

        return static::query()->where('state', $state)->distinct()->orderBy('district')->pluck('district', 'district')->all();
    }

    /** All distinct PIN codes of a district. */
    public static function pincodesForDistrict(string $state, string $district): array
    {
        return static::query()->where('state', $state)->where('district', $district)
            ->distinct()->orderBy('pincode')->pluck('pincode')->all();
    }

    /** Search post offices / localities by name or PIN code, for the admin picker. */
    public static function searchLocalities(string $search, ?string $state = null, ?string $district = null, int $limit = 50): array
    {
        $search = trim($search);
        if (mb_strlen($search) < 2) {
            return [];
        }

        return static::query()
            ->when($state, fn ($q) => $q->where('state', $state))
            ->when($district, fn ($q) => $q->where('district', $district))
            ->where(fn ($q) => ctype_digit($search)
                ? $q->where('pincode', 'like', $search . '%')
                : $q->where('office_name', 'like', '%' . $search . '%'))
            ->orderBy('office_name')
            ->limit($limit)
            ->get()
            ->mapWithKeys(fn (self $row) => [$row->pincode . '|' . $row->id => "{$row->office_name}, {$row->district} – {$row->pincode}"])
            ->all();
    }

    /** All localities of a district for the admin dropdown: "pin|id" => "Office – PIN". */
    public static function localitiesInDistrict(?string $state, ?string $district): array
    {
        if (! $state || ! $district) {
            return [];
        }

        return static::query()->where('state', $state)->where('district', $district)
            ->orderBy('office_name')->get()
            ->mapWithKeys(fn (self $row) => [$row->pincode . '|' . $row->id => "{$row->office_name} – {$row->pincode}"])
            ->all();
    }

    /** Locality suggestions for customers: "Office, District – PIN" for the given PIN codes. */
    public static function suggestionsFor(array $pincodes): array
    {
        if (! $pincodes) {
            return [];
        }

        return static::query()->whereIn('pincode', $pincodes)->orderBy('office_name')->get()
            ->map(fn (self $row) => "{$row->office_name}, {$row->district} – {$row->pincode}")
            ->unique()->values()->all();
    }

    /** Locality names for a list of PIN codes: pincode => "Office A, Office B". */
    public static function localityNames(array $pincodes): Collection
    {
        if (! $pincodes) {
            return collect();
        }

        return static::query()->whereIn('pincode', $pincodes)->orderBy('office_name')->get(['pincode', 'office_name'])
            ->groupBy('pincode')
            ->map(fn ($rows) => $rows->pluck('office_name')->unique()->implode(', '));
    }
}

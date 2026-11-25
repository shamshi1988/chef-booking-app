<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A bookable service (Private Chef, Electrician, Plumber, AC Repair, ...).
 *
 * `form_fields` holds the extra questions shown on this service's booking form, e.g.
 * [{"key":"ac_type","label":"AC type","type":"select","options":["Split","Window"],"required":true}]
 * Supported types: text, number, select, multiselect, textarea.
 */
class ServiceCategory extends Model
{
    public const FIELD_TYPES = [
        'text' => 'Short text',
        'number' => 'Number',
        'select' => 'Dropdown (pick one)',
        'multiselect' => 'Checkboxes (pick many)',
        'textarea' => 'Long text',
    ];

    /** Icon keys understood by <x-service-icon>. */
    public const ICONS = [
        'chef' => 'Chef hat',
        'bolt' => 'Electrician (bolt)',
        'wrench' => 'Plumber / repair (wrench)',
        'snowflake' => 'AC / cooling',
        'heart' => 'Health / physiotherapy',
        'sparkles' => 'Cleaning',
        'hammer' => 'Carpenter',
        'appliance' => 'Appliance repair',
        'bug' => 'Pest control',
        'brush' => 'Painting',
        'home' => 'General home service',
    ];

    protected $fillable = [
        'name',
        'slug',
        'tagline',
        'description',
        'icon',
        'starting_price',
        'price_unit',
        'highlights',
        'form_fields',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'highlights' => 'array',
        'form_fields' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'starting_price' => 'decimal:2',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }

    public function providerProfiles(): HasMany
    {
        return $this->hasMany(ProviderProfile::class);
    }

    public function serviceAreas(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(ServiceArea::class);
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }

    /** Valid, normalised form fields for this service's booking form. */
    public function fields(): array
    {
        return collect($this->form_fields ?? [])
            ->filter(fn ($field) => ! empty($field['key']) && ! empty($field['label']))
            ->map(fn ($field) => [
                'key' => (string) $field['key'],
                'label' => (string) $field['label'],
                'type' => array_key_exists($field['type'] ?? '', self::FIELD_TYPES) ? $field['type'] : 'text',
                'options' => array_values(array_filter((array) ($field['options'] ?? []), 'strlen')),
                'required' => (bool) ($field['required'] ?? false),
                'placeholder' => $field['placeholder'] ?? null,
            ])
            ->values()
            ->all();
    }

    public function priceLabel(): ?string
    {
        if ($this->starting_price === null) {
            return null;
        }

        return '₹' . number_format((float) $this->starting_price) . ($this->price_unit ? ' ' . $this->price_unit : '');
    }
}

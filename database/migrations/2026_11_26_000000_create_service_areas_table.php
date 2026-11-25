<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Service areas: the PIN codes where bookings are accepted.
 * An area can optionally be limited to some services (no rows in the pivot = all services).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_areas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('city');
            $table->json('pincodes');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('service_area_service_category', function (Blueprint $table) {
            $table->foreignId('service_area_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_category_id')->constrained()->cascadeOnDelete();
            $table->primary(['service_area_id', 'service_category_id'], 'area_category_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_area_service_category');
        Schema::dropIfExists('service_areas');
    }
};

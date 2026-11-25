<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Reference list of Indian PIN codes (India Post "All India Pincode Directory").
 * Filled by: php artisan pincodes:import <file.csv>
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pincode_directory', function (Blueprint $table) {
            $table->id();
            $table->char('pincode', 6)->index();
            $table->string('office_name');
            $table->string('district')->index();
            $table->string('state')->index();
            $table->unique(['pincode', 'office_name']);
            $table->index(['state', 'district']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pincode_directory');
    }
};

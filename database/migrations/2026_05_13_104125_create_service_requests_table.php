<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('event_date');
            $table->time('event_time');
            $table->integer('guest_count');
            $table->decimal('budget_range_min', 10, 2)->nullable();
            $table->decimal('budget_range_max', 10, 2)->nullable();
            $table->json('cuisine_preferences')->nullable();
            $table->text('details')->nullable();
            $table->string('status')->default('open'); // open, closed, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};

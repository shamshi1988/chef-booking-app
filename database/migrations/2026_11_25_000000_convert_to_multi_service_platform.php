<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Turns the chef-only marketplace into a multi-service platform
 * (chef, electrician, plumber, AC repair, physiotherapy, ...).
 *
 * - New `service_categories` table; each category defines its own booking-form questions.
 * - chef_profiles  -> provider_profiles (+ service_category_id, is_verified)
 * - chef_id        -> provider_id (service_requests, proposals, ratings)
 * - service_requests gets category, address and per-category `answers`;
 *   the chef-only guest_count / cuisine_preferences values move into `answers`.
 * - Role "chef" -> "provider".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('icon')->default('wrench');
            $table->decimal('starting_price', 10, 2)->nullable();
            $table->string('price_unit')->nullable();
            $table->json('highlights')->nullable();
            $table->json('form_fields')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Existing requests were all chef bookings; give them a category to belong to.
        // ServiceCategorySeeder fills in the full details later (matched by slug).
        $chefCategoryId = DB::table('service_categories')->insertGetId([
            'name' => 'Private Chef',
            'slug' => 'private-chef',
            'icon' => 'chef',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // --- Provider profiles -------------------------------------------------
        Schema::rename('chef_profiles', 'provider_profiles');
        Schema::table('provider_profiles', function (Blueprint $table) {
            $table->renameColumn('specialties', 'skills');
        });
        Schema::table('provider_profiles', function (Blueprint $table) {
            $table->foreignId('service_category_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->boolean('is_verified')->default(false)->after('avatar_url');
        });
        DB::table('provider_profiles')->update(['service_category_id' => $chefCategoryId]);

        // --- Service requests --------------------------------------------------
        Schema::table('service_requests', function (Blueprint $table) {
            $table->foreignId('service_category_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->json('answers')->nullable()->after('details');
            $table->string('address')->nullable()->after('answers');
            $table->string('city')->nullable()->after('address');
            $table->string('pincode', 10)->nullable()->after('city');
        });
        Schema::table('service_requests', fn (Blueprint $table) => $table->renameColumn('chef_id', 'provider_id'));
        Schema::table('service_requests', fn (Blueprint $table) => $table->renameColumn('event_date', 'preferred_date'));
        Schema::table('service_requests', fn (Blueprint $table) => $table->renameColumn('event_time', 'preferred_time'));
        // Time becomes a slot ("morning", "evening", ...) instead of an exact clock time.
        Schema::table('service_requests', fn (Blueprint $table) => $table->string('preferred_time')->nullable()->change());

        foreach (DB::table('service_requests')->get() as $row) {
            $answers = array_filter([
                'guest_count' => $row->guest_count,
                'cuisine' => json_decode($row->cuisine_preferences ?? 'null', true) ?: null,
            ], fn ($value) => $value !== null);

            DB::table('service_requests')->where('id', $row->id)->update([
                'service_category_id' => $chefCategoryId,
                'answers' => json_encode((object) $answers),
            ]);
        }

        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn(['guest_count', 'cuisine_preferences']);
        });

        // --- Proposals & ratings -----------------------------------------------
        Schema::table('proposals', fn (Blueprint $table) => $table->renameColumn('chef_id', 'provider_id'));
        Schema::table('proposals', fn (Blueprint $table) => $table->renameColumn('menu_details', 'message'));
        Schema::table('ratings', fn (Blueprint $table) => $table->renameColumn('chef_id', 'provider_id'));

        // --- General enquiries (contact / call-back form) ------------------------
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->foreignId('service_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('city')->nullable();
            $table->text('message');
            $table->string('status')->default('new'); // new, contacted, closed
            $table->timestamps();
        });

        // --- Roles ---------------------------------------------------------------
        DB::table('roles')->where('name', 'chef')->update(['name' => 'provider']);
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        DB::table('roles')->where('name', 'provider')->update(['name' => 'chef']);
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        Schema::dropIfExists('enquiries');

        Schema::table('ratings', fn (Blueprint $table) => $table->renameColumn('provider_id', 'chef_id'));
        Schema::table('proposals', fn (Blueprint $table) => $table->renameColumn('message', 'menu_details'));
        Schema::table('proposals', fn (Blueprint $table) => $table->renameColumn('provider_id', 'chef_id'));

        Schema::table('service_requests', function (Blueprint $table) {
            $table->integer('guest_count')->default(1);
            $table->json('cuisine_preferences')->nullable();
        });
        foreach (DB::table('service_requests')->get() as $row) {
            $answers = json_decode($row->answers ?? '{}', true) ?: [];
            DB::table('service_requests')->where('id', $row->id)->update([
                'guest_count' => (int) ($answers['guest_count'] ?? 1),
                'cuisine_preferences' => isset($answers['cuisine']) ? json_encode($answers['cuisine']) : null,
            ]);
        }
        Schema::table('service_requests', fn (Blueprint $table) => $table->renameColumn('preferred_time', 'event_time'));
        Schema::table('service_requests', fn (Blueprint $table) => $table->renameColumn('preferred_date', 'event_date'));
        Schema::table('service_requests', fn (Blueprint $table) => $table->renameColumn('provider_id', 'chef_id'));
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_category_id');
            $table->dropColumn(['answers', 'address', 'city', 'pincode']);
        });

        Schema::table('provider_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_category_id');
            $table->dropColumn('is_verified');
        });
        Schema::table('provider_profiles', fn (Blueprint $table) => $table->renameColumn('skills', 'specialties'));
        Schema::rename('provider_profiles', 'chef_profiles');

        Schema::dropIfExists('service_categories');
    }
};

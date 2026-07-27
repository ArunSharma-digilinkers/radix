<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The distributor network and export markets.
 *
 * Brief §6 calls the 650+ dealer network a strong trust signal that is currently
 * "buried in text", and §4 asks for a searchable locator as a new page. That page
 * is only as good as this table, so it is built to be searched: city, state and
 * pincode are indexed, and coordinates are stored for nearest-first ordering.
 *
 * Coordinates are nullable. The client's dealer list may arrive without them
 * (open question 3 in the plan), and a dealer with an address but no lat/lng must
 * still be findable by city.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dealers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // 'retail' | 'distributor' | 'service_centre'
            $table->string('type', 32)->default('retail')->index();

            $table->string('contact_person')->nullable();
            $table->string('phone', 32)->nullable();
            $table->string('whatsapp', 32)->nullable();
            $table->string('email')->nullable();

            $table->string('address_line')->nullable();
            $table->string('city', 128)->index();
            $table->string('state', 128)->index();
            $table->string('pincode', 16)->nullable()->index();
            $table->char('country_code', 2)->default('IN')->index();

            // 7 decimal places ≈ 11mm, far beyond what a store locator needs.
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            // Bounding-box lookups scan both columns together.
            $table->index(['latitude', 'longitude']);
        });

        Schema::create('export_markets', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('country_name');
            $table->char('iso_code', 2)->unique();

            /*
             * ISO 3166-1 numeric. This is the join between the database and the
             * homepage map: scripts/build-maps.mjs highlights countries by the
             * same numeric id. Keeping it here means the map and the export page
             * can be reconciled instead of drifting apart.
             */
            $table->unsignedSmallInteger('iso_numeric')->nullable()->unique();

            $table->json('blurb')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('export_markets');
        Schema::dropIfExists('dealers');
    }
};

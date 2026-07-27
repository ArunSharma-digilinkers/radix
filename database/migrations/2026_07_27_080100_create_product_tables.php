<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The eight product lines from brief §4, plus everything a detail page needs:
 * specs, variants, FAQs, downloadable datasheets.
 *
 * Two shapes worth understanding before adding to this:
 *
 * - A `product` is a *line* (Inverter Batteries), not an SKU. Individual models
 *   are `product_variants`, which is what the brief calls the "model/variant list
 *   with downloadable datasheet PDF".
 * - `product_components` exists for the Solar Power Generating System, which the
 *   brief singles out (§6): panel + battery + inverter + charge controller sold as
 *   one warrantied bundle, with each part also explained separately. A normal
 *   battery line simply has none.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name');
            $table->json('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_category_id')->nullable()->constrained()->nullOnDelete();

            // Permanent, human-readable URL segment: /products/solar-batteries.
            // Never a query string — see brief §6.
            $table->string('slug')->unique();

            $table->json('name');
            $table->json('pitch')->nullable();          // one-liner for the hub grid
            $table->json('description')->nullable();
            $table->json('use_cases')->nullable();

            // 'battery' | 'solar_system'. A string with model constants rather than
            // an enum column: extending an enum needs a destructive ALTER, which
            // this project does not allow (CLAUDE.md §1).
            $table->string('kind', 32)->default('battery')->index();

            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('model_code')->index();
            $table->json('name')->nullable();

            // Typed because the finder and comparison tables need to sort and
            // filter on them. Free-form extras belong in product_specs instead.
            $table->decimal('capacity_ah', 8, 2)->nullable();
            $table->decimal('voltage', 6, 2)->nullable();
            $table->unsignedSmallInteger('warranty_months')->nullable();
            $table->string('dimensions_mm', 64)->nullable();
            $table->decimal('weight_kg', 8, 2)->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['product_id', 'model_code']);
        });

        /* Free-form spec rows for the line as a whole. */
        Schema::create('product_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->json('label');
            $table->json('value');
            $table->string('group', 64)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->json('question');
            $table->json('answer');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        /*
         * Datasheets, certificates and manuals. Attached to a line, or to a single
         * variant when the document is model-specific.
         */
        Schema::create('product_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->json('title');
            // 'datasheet' | 'certificate' | 'manual' | 'warranty'
            $table->string('type', 32)->default('datasheet')->index();
            $table->string('disk', 32)->default('public');
            $table->string('path');
            $table->string('mime_type', 128)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->unsignedInteger('download_count')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            // 'panel' | 'battery' | 'inverter' | 'charge_controller'
            $table->string('type', 32)->index();
            $table->json('name');
            $table->json('description')->nullable();
            $table->json('specs')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_components');
        Schema::dropIfExists('product_documents');
        Schema::dropIfExists('product_faqs');
        Schema::dropIfExists('product_specs');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
    }
};

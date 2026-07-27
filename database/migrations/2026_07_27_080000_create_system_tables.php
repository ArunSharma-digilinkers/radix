<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cross-cutting tables: editable settings, attached media, and the redirect map.
 *
 * Translatable text is stored as JSON (spatie/laravel-translatable): one column
 * holding {"en": "…", "hi": "…"}. English ships now; Hindi is switched on later
 * without a schema change, which matters because migrations here are additive
 * only. See CLAUDE.md §4.
 */
return new class extends Migration
{
    public function up(): void
    {
        /*
         * Single source for the figures that appear all over the site — 25+ years,
         * 650+ distributors, 10L+ customers. The old site contradicts itself on
         * team size precisely because those numbers were typed into templates.
         */
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('group', 64)->default('general')->index();
            $table->json('value')->nullable();
            // 'string' | 'text' | 'html' | 'number' | 'boolean' | 'json'
            // A string, not an enum: adding a type must never require an ALTER.
            $table->string('cast', 32)->default('string');
            $table->boolean('is_translatable')->default(false);
            $table->string('label')->nullable();
            $table->text('help')->nullable();
            $table->timestamps();
        });

        /*
         * Polymorphic media. Files live on a disk; this table records where and
         * what they belong to. `alt` is translatable and required by the
         * accessibility commitment in the brief (§7) — enforced in the admin, not
         * at the database level, so an import can never be blocked halfway.
         */
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->morphs('mediable');
            $table->string('collection', 64)->default('default');
            $table->string('disk', 32)->default('public');
            $table->string('path');
            $table->string('filename');
            $table->string('mime_type', 128)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->json('alt')->nullable();
            $table->json('caption')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['mediable_type', 'mediable_id', 'collection'], 'media_owner_collection_index');
        });

        /*
         * 301 map for the launch cutover. The current site uses query-string URLs
         * like ?row=31 (brief §6); every one of them needs to land on its clean
         * equivalent or the existing search rankings are thrown away.
         *
         * `from_path` includes the query string when there is one, which is the
         * whole point here.
         */
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            // 512 chars leaves room for a query string. At utf8mb4 that is a
            // 2048-byte index key, inside InnoDB's 3072-byte limit.
            $table->string('from_path', 512)->unique();
            $table->string('to_path', 512);
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->unsignedBigInteger('hits')->default(0);
            $table->timestamp('last_hit_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redirects');
        Schema::dropIfExists('media');
        Schema::dropIfExists('site_settings');
    }
};

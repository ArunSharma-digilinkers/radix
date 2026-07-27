<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Careers. Brief §4: an openings list, or a "send your CV" fallback when there
 * are none — which is why applications may exist without a job opening.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_openings', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('title');
            $table->string('department')->nullable()->index();
            $table->string('location')->nullable();
            // 'full_time' | 'part_time' | 'contract' | 'internship'
            $table->string('employment_type', 32)->default('full_time')->index();
            $table->json('description')->nullable();
            $table->json('requirements')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->date('closes_on')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            // Null for a speculative CV sent when nothing is advertised.
            $table->foreignId('job_opening_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name');
            $table->string('email');
            $table->string('phone', 32)->nullable();
            $table->string('resume_disk', 32)->default('local');
            $table->string('resume_path')->nullable();
            $table->text('cover_note')->nullable();

            // 'new' | 'reviewing' | 'shortlisted' | 'rejected' | 'hired'
            $table->string('status', 32)->default('new')->index();
            $table->text('internal_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('job_openings');
    }
};

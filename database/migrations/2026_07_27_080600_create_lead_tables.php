<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lead capture — the reason this site exists. Brief §2: "Increase qualified
 * enquiries via clear CTAs, WhatsApp integration, and simplified forms."
 *
 * Enquiries are never hard-deleted (soft deletes only): a lead removed by mistake
 * is lost revenue, and the sales role needs an audit trail.
 *
 * Nothing here is translatable. These are records of what a person typed, not
 * content we author.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();

            // 'product' | 'dealer' | 'export' | 'career' | 'general'
            // Routing and the admin inbox filter on this.
            $table->string('type', 32)->default('general')->index();

            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 32)->nullable();
            $table->string('company')->nullable();
            $table->string('city')->nullable();
            $table->char('country_code', 2)->nullable();
            $table->text('message')->nullable();

            // Set when the enquiry came from a product page, so sales can see what
            // was being looked at without asking.
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();

            // 'new' | 'contacted' | 'qualified' | 'won' | 'lost'
            $table->string('status', 32)->default('new')->index();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('internal_notes')->nullable();
            $table->timestamp('responded_at')->nullable();

            // Context for spam triage and for measuring which pages convert.
            $table->string('source_url', 512)->nullable();
            $table->string('referrer', 512)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 512)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // The inbox default view: newest open enquiries of a given type.
            $table->index(['status', 'created_at']);
        });

        Schema::create('callback_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 32);
            $table->string('preferred_time')->nullable();
            // 'new' | 'called' | 'unreachable' | 'closed'
            $table->string('status', 32)->default('new')->index();
            $table->text('internal_notes')->nullable();
            $table->string('source_url', 512)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('callback_requests');
        Schema::dropIfExists('enquiries');
    }
};

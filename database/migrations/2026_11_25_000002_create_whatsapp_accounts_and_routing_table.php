<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete(); // null = shared across businesses
            $table->string('name');
            $table->string('phone_number')->nullable();
            $table->string('phone_number_id')->nullable();
            $table->string('waba_id')->nullable();
            $table->text('access_token')->nullable(); // encrypted
            $table->string('webhook_verify_token')->nullable();
            $table->boolean('is_shared')->default(false);
            $table->string('status')->default('disconnected'); // connected, disconnected, rate_limited, error
            $table->text('last_error')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });

        Schema::create('business_routing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('keywords'); // array of keyword strings e.g. ["website", "software", "seo"]
            $table->string('priority')->default('normal'); // high, normal, low
            $table->decimal('min_confidence', 3, 2)->default(0.85);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('routing_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_account_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->text('incoming_text');
            $table->foreignId('matched_business_id')->nullable()->constrained('businesses')->nullOnDelete();
            $table->decimal('confidence', 3, 2)->default(0.00);
            $table->string('status'); // routed, ambiguous, unassigned_review, manual_override
            $table->string('reason')->nullable();
            $table->json('details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routing_logs');
        Schema::dropIfExists('business_routing_rules');
        Schema::dropIfExists('whatsapp_accounts');
    }
};

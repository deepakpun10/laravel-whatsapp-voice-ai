<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete(); // null = unassigned review
            $table->foreignId('whatsapp_account_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('source')->default('whatsapp'); // whatsapp, call, manual, referral
            $table->string('lead_status')->default('new'); // new, interested, qualified, follow_up, converted, lost
            $table->string('requirement')->nullable();
            $table->string('budget')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('consent_status')->default(true);
            $table->json('tags')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['phone', 'business_id']);
        });

        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('whatsapp_account_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel')->default('whatsapp');
            $table->string('status')->default('active'); // active, closed, unassigned_review
            $table->string('ai_mode')->default('active'); // active, paused (human takeover)
            $table->unsignedBigInteger('assigned_agent_id')->nullable();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('takeover_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('takeover_at')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'status']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->string('sender_type'); // contact, ai, human, system
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->string('message_id')->nullable()->unique(); // Meta wamid for idempotency
            $table->string('message_type')->default('text'); // text, media, document, template
            $table->text('content')->nullable();
            $table->string('media_url')->nullable();
            $table->string('media_type')->nullable();
            $table->string('status')->default('sent'); // queued, sent, delivered, read, failed
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('contacts');
    }
};

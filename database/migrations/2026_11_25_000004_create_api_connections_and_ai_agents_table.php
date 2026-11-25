<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete(); // null = shared/global
            $table->string('name');
            $table->string('provider_type'); // ai, voice, telephony, vector, notifications
            $table->string('provider_name'); // openai, sarvam, groq, anthropic, twilio, qdrant, custom
            $table->string('base_url')->nullable();
            $table->text('api_key')->nullable(); // encrypted
            $table->string('model_or_agent_id')->nullable();
            $table->string('organization_id')->nullable();
            $table->json('custom_headers')->nullable();
            $table->string('priority')->default('primary'); // primary, fallback_1, fallback_2
            $table->integer('timeout')->default(30);
            $table->boolean('is_active')->default(true);
            $table->string('last_health_status')->default('unknown'); // healthy, error, timeout, unconfigured
            $table->timestamp('last_health_checked_at')->nullable();
            $table->text('last_health_error')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('api_connection_id')->nullable()->constrained('api_connections')->nullOnDelete();
            $table->foreignId('fallback_connection_id')->nullable()->constrained('api_connections')->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('model')->default('gpt-4o-mini');
            $table->text('system_instructions')->nullable();
            $table->string('personality_tone')->default('professional');
            $table->json('supported_languages')->nullable();
            $table->decimal('confidence_threshold', 3, 2)->default(0.75);
            $table->integer('max_response_tokens')->default(600);
            $table->string('unknown_query_policy')->default('ask_clarification'); // ask_clarification, human_handover, silent
            $table->string('human_handover_policy')->default('notify_and_pause');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['business_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_agents');
        Schema::dropIfExists('api_connections');
    }
};

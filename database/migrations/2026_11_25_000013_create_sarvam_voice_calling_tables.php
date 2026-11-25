<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Sarvam Phone Numbers Inventory
        Schema::create('sarvam_phone_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('api_connection_id')->nullable()->constrained('api_connections')->nullOnDelete();
            $table->foreignId('ai_agent_id')->nullable()->constrained('ai_agents')->nullOnDelete();
            $table->string('phone_number')->unique();
            $table->string('label')->nullable();
            $table->string('status')->default('active'); // active, archived, pending_verification
            $table->boolean('inbound_enabled')->default(true);
            $table->boolean('outbound_enabled')->default(true);
            $table->string('inbound_deployment_id')->nullable();
            $table->string('telephony_connection_id')->nullable();
            $table->string('provider')->default('sarvam'); // sarvam, exotel, twilio, vobiz
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'status']);
        });

        // 2. Calling Settings (General, Schedule, Consent, Retries)
        Schema::create('calling_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->unique()->constrained()->cascadeOnDelete();
            $table->boolean('is_automated_calling_enabled')->default(false);
            $table->boolean('inbound_enabled')->default(true);
            $table->boolean('outbound_enabled')->default(true);
            $table->foreignId('default_phone_number_id')->nullable()->constrained('sarvam_phone_numbers')->nullOnDelete();
            $table->foreignId('default_ai_agent_id')->nullable()->constrained('ai_agents')->nullOnDelete();
            $table->json('allowed_days')->nullable(); // ["monday","tuesday","wednesday","thursday","friday","saturday"]
            $table->string('start_time')->default('09:30');
            $table->string('end_time')->default('19:30');
            $table->string('timezone')->default('Asia/Kolkata');
            $table->boolean('emergency_pause')->default(false);
            $table->unsignedInteger('min_lead_score')->default(30);
            $table->unsignedInteger('min_budget')->default(0);
            $table->boolean('require_explicit_consent')->default(true);
            $table->unsignedSmallInteger('max_retry_attempts')->default(3);
            $table->unsignedSmallInteger('retry_interval_minutes')->default(60);
            $table->boolean('retry_on_busy')->default(true);
            $table->boolean('retry_on_no_answer')->default(true);
            $table->boolean('stop_on_decline')->default(true);
            $table->timestamps();
        });

        // 3. Extend Calls Table with Voice Telephony Columns
        Schema::table('calls', function (Blueprint $table) {
            $table->foreignId('ai_agent_id')->nullable()->after('api_connection_id')->constrained('ai_agents')->nullOnDelete();
            $table->foreignId('sarvam_phone_number_id')->nullable()->after('ai_agent_id')->constrained('sarvam_phone_numbers')->nullOnDelete();
            $table->string('campaign_id')->nullable()->after('provider_call_id');
            $table->string('sarvam_session_id')->nullable()->after('campaign_id');
            $table->timestamp('started_at')->nullable()->after('duration_seconds');
            $table->timestamp('ended_at')->nullable()->after('started_at');
            $table->decimal('cost', 8, 4)->nullable()->after('recording_url');
            $table->json('extracted_facts')->nullable()->after('outcome');
            $table->boolean('requires_followup')->default(false)->after('extracted_facts');
            $table->timestamp('consent_verified_at')->nullable()->after('requires_followup');
            $table->string('consent_source')->nullable()->after('consent_verified_at');
        });

        // 4. Calling Workflows (Visual Automation Rules for Voice Calls)
        Schema::create('calling_workflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('ai_agent_id')->nullable()->constrained('ai_agents')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('trigger_type'); // new_enquiry, callback_requested, call_me_button, new_lead, score_threshold, no_response, manual_action
            $table->json('conditions')->nullable(); // group of conditions (business, score, budget, intent, stage, consent, hours)
            $table->json('actions'); // initiate_call, schedule_call, assign_human, send_whatsapp, update_stage, create_followup, add_note, stop_workflow
            $table->unsignedSmallInteger('priority')->default(10);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('execution_count')->default(0);
            $table->timestamps();

            $table->index(['business_id', 'is_active', 'priority']);
        });

        // 5. Calling Workflow Runs
        Schema::create('calling_workflow_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calling_workflow_id')->constrained('calling_workflows')->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('call_id')->nullable()->constrained('calls')->nullOnDelete();
            $table->string('idempotency_key')->unique();
            $table->string('status')->default('completed'); // pending, running, completed, failed, skipped
            $table->json('logs')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        // 6. Call Consents & Audit Trail
        Schema::create('call_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone');
            $table->boolean('consent_status')->default(true);
            $table->string('consent_source')->default('whatsapp_interactive'); // whatsapp_interactive, web_form, verbal_call, manual_entry
            $table->string('purpose')->default('sales_callback');
            $table->timestamp('consented_at')->useCurrent();
            $table->timestamp('opt_out_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['phone', 'consent_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_consents');
        Schema::dropIfExists('calling_workflow_runs');
        Schema::dropIfExists('calling_workflows');

        Schema::table('calls', function (Blueprint $table) {
            $table->dropForeign(['ai_agent_id']);
            $table->dropForeign(['sarvam_phone_number_id']);
            $table->dropColumn([
                'ai_agent_id',
                'sarvam_phone_number_id',
                'campaign_id',
                'sarvam_session_id',
                'started_at',
                'ended_at',
                'cost',
                'extracted_facts',
                'requires_followup',
                'consent_verified_at',
                'consent_source',
            ]);
        });

        Schema::dropIfExists('calling_settings');
        Schema::dropIfExists('sarvam_phone_numbers');
    }
};

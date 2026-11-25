<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Interactive Message Templates
        Schema::create('interactive_message_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('category')->default('sales_qualification'); // sales_qualification, service_selection, budget_qualification, order_booking, general
            $table->string('interactive_type')->default('button'); // button, list, cta_url
            $table->string('header_text', 100)->nullable();
            $table->text('body_text');
            $table->string('footer_text', 100)->nullable();
            $table->string('list_button_label', 50)->nullable(); // e.g. "Select Service"
            $table->string('cta_url', 500)->nullable();
            $table->string('cta_display_text', 50)->nullable();
            $table->string('language', 20)->default('en'); // en, hi, hi_en
            $table->string('status', 20)->default('active'); // active, inactive
            $table->boolean('ai_availability')->default(true);
            $table->string('trigger_intent')->nullable(); // e.g. website_inquiry, budget_inquiry
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'interactive_type']);
            $table->unique(['business_id', 'slug']);
        });

        // 2. Interactive Message Buttons (Up to 3 reply buttons per template)
        Schema::create('interactive_message_buttons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('interactive_message_templates')->cascadeOnDelete();
            $table->string('button_id', 100); // e.g. btn_website_corp
            $table->string('title', 50); // Max 20 chars on WhatsApp
            $table->string('action_type')->default('set_service'); // set_service, set_budget, ai_instruction, callback_request, link
            $table->text('action_value')->nullable(); // e.g. "Website Development" or "15000"
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index(['template_id', 'position']);
        });

        // 3. Interactive Message Sections (For List Messages)
        Schema::create('interactive_message_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('interactive_message_templates')->cascadeOnDelete();
            $table->string('title', 50); // Max 24 chars on WhatsApp
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index(['template_id', 'position']);
        });

        // 4. Interactive Message Rows (Items inside List Sections, max 10 total)
        Schema::create('interactive_message_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('interactive_message_sections')->cascadeOnDelete();
            $table->string('row_id', 100); // e.g. row_web_ecommerce
            $table->string('title', 50); // Max 24 chars on WhatsApp
            $table->string('description', 100)->nullable(); // Max 72 chars on WhatsApp
            $table->string('action_type')->default('set_service');
            $table->text('action_value')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index(['section_id', 'position']);
        });

        // 5. Interactive Automation Rules
        Schema::create('interactive_automation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ai_agent_id')->nullable()->constrained('ai_agents')->nullOnDelete();
            $table->foreignId('template_id')->constrained('interactive_message_templates')->cascadeOnDelete();
            $table->string('name');
            $table->string('trigger_type')->default('intent_detected'); // intent_detected, keywords, first_inquiry, budget_stage
            $table->json('trigger_keywords')->nullable(); // e.g. ["website", "site", "web"]
            $table->string('condition_lead_stage')->nullable(); // new, interested, qualified
            $table->unsignedSmallInteger('priority')->default(10);
            $table->boolean('is_active')->default(true);
            $table->string('send_mode')->default('automatic'); // automatic, ai_assisted, first_inquiry_only
            $table->timestamps();

            $table->index(['business_id', 'is_active', 'priority'], 'iar_bus_act_pri_idx');
        });

        // 6. Interactive Agent Settings
        Schema::create('interactive_agent_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_agent_id')->unique()->constrained('ai_agents')->cascadeOnDelete();
            $table->string('mode')->default('ai_assisted'); // manual, rule_based, ai_assisted
            $table->boolean('enable_interactive')->default(true);
            $table->boolean('prefer_buttons_over_text')->default(true);
            $table->unsignedSmallInteger('max_buttons_per_message')->default(3);
            $table->boolean('allow_list_messages')->default(true);
            $table->boolean('allow_cta_messages')->default(true);
            $table->unsignedSmallInteger('max_interactive_per_conversation')->default(5);
            $table->unsignedInteger('cooldown_seconds')->default(60);
            $table->decimal('min_confidence', 3, 2)->default(0.75);
            $table->timestamps();
        });

        // 7. Interactive Message Events (Tracking sends and clicks for analytics)
        Schema::create('interactive_message_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('interactive_message_templates')->nullOnDelete();
            $table->string('interactive_type')->default('button'); // button, list, cta_url
            $table->string('event_type')->default('sent'); // sent, clicked, delivered, read
            $table->string('selected_id', 100)->nullable();
            $table->string('selected_title')->nullable();
            $table->string('wamid')->nullable()->index();
            $table->json('raw_meta_payload')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'event_type', 'created_at'], 'ime_bus_evt_idx');
            $table->index(['conversation_id', 'selected_id'], 'ime_conv_sel_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interactive_message_events');
        Schema::dropIfExists('interactive_agent_settings');
        Schema::dropIfExists('interactive_automation_rules');
        Schema::dropIfExists('interactive_message_rows');
        Schema::dropIfExists('interactive_message_sections');
        Schema::dropIfExists('interactive_message_buttons');
        Schema::dropIfExists('interactive_message_templates');
    }
};

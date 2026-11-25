<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_agents', function (Blueprint $table) {
            $table->string('agent_type')->default('whatsapp_chat')->after('name'); // whatsapp_chat, voice_calling, multimodal
            $table->json('voice_config')->nullable()->after('human_handover_policy'); // sarvam agent id, voice accent, language, etc.
        });
    }

    public function down(): void
    {
        Schema::table('ai_agents', function (Blueprint $table) {
            $table->dropColumn(['agent_type', 'voice_config']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('api_connection_id')->nullable()->constrained()->nullOnDelete();
            $table->string('direction')->default('outgoing'); // incoming, outgoing
            $table->string('call_type')->default('ai_agent'); // ai_agent, callback, human
            $table->string('provider_call_id')->nullable()->unique();
            $table->string('from_number')->nullable();
            $table->string('to_number');
            $table->integer('duration_seconds')->default(0);
            $table->string('status')->default('initiated'); // initiated, ringing, in_progress, completed, failed, busy, no_answer
            $table->text('transcript')->nullable();
            $table->text('summary')->nullable();
            $table->string('recording_url')->nullable();
            $table->string('outcome')->nullable(); // qualified, callback_scheduled, price_inquiry, converted, not_interested
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};

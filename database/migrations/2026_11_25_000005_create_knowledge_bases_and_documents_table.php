<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('knowledge_bases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('status')->default('ready'); // ready, indexing, error
            $table->timestamps();

            $table->unique(['business_id', 'slug']);
        });

        Schema::create('ai_agent_knowledge_base', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_agent_id')->constrained()->cascadeOnDelete();
            $table->foreignId('knowledge_base_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['ai_agent_id', 'knowledge_base_id']);
        });

        Schema::create('kb_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knowledge_base_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('source_type')->default('file'); // file, url, text
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable(); // pdf, docx, txt, csv
            $table->unsignedBigInteger('file_size')->default(0);
            $table->unsignedInteger('chunk_count')->default(0);
            $table->string('status')->default('pending'); // pending, processing, indexed, failed
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('indexed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('kb_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kb_document_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('chunk_index')->default(0);
            $table->longText('content');
            $table->unsignedInteger('token_count')->default(0);
            $table->string('vector_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kb_chunks');
        Schema::dropIfExists('kb_documents');
        Schema::dropIfExists('ai_agent_knowledge_base');
        Schema::dropIfExists('knowledge_bases');
    }
};

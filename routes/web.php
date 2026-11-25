<?php

use App\Http\Controllers\SarvamWebhookController;
use App\Http\Controllers\WhatsAppWebhookController;
use App\Livewire\Admin\AiAgents\AgentCreate;
use App\Livewire\Admin\AiAgents\AgentEdit;
use App\Livewire\Admin\AiAgents\AgentIndex;
use App\Livewire\Admin\AiAgents\AgentShow;
use App\Livewire\Admin\Analytics\AnalyticsIndex;
use App\Livewire\Admin\Businesses\BusinessCreate;
use App\Livewire\Admin\Businesses\BusinessEdit;
use App\Livewire\Admin\Businesses\BusinessIndex;
use App\Livewire\Admin\Businesses\BusinessShow;
use App\Livewire\Admin\Calls\CallCreate;
use App\Livewire\Admin\Calls\CallIndex;
use App\Livewire\Admin\Calls\CallingSettings;
use App\Livewire\Admin\Calls\CallShow;
use App\Livewire\Admin\Campaigns\CampaignIndex;
use App\Livewire\Admin\Contacts\ContactCreate;
use App\Livewire\Admin\Contacts\ContactEdit;
use App\Livewire\Admin\Contacts\ContactIndex;
use App\Livewire\Admin\Contacts\ContactShow;
use App\Livewire\Admin\Conversations\ConversationIndex;
use App\Livewire\Admin\Dashboard\Dashboard;
use App\Livewire\Admin\FollowUps\FollowUpIndex;
use App\Livewire\Admin\Integrations\IntegrationIndex;
use App\Livewire\Admin\Interactive\InteractiveCreate;
use App\Livewire\Admin\Interactive\InteractiveEdit;
use App\Livewire\Admin\Interactive\InteractiveIndex;
use App\Livewire\Admin\Interactive\InteractiveShow;
use App\Livewire\Admin\KnowledgeBases\KnowledgeBaseCreate;
use App\Livewire\Admin\KnowledgeBases\KnowledgeBaseEdit;
use App\Livewire\Admin\KnowledgeBases\KnowledgeBaseIndex;
use App\Livewire\Admin\KnowledgeBases\KnowledgeBaseShow;
use App\Livewire\Admin\Routing\RoutingCreate;
use App\Livewire\Admin\Routing\RoutingEdit;
use App\Livewire\Admin\Routing\RoutingIndex;
use App\Livewire\Admin\Routing\RoutingShow;
use App\Livewire\Admin\Settings\SettingsIndex;
use App\Livewire\Admin\Telephony\PhoneNumbersIndex;
use App\Livewire\Admin\Workflows\CallingWorkflowIndex;
use App\Livewire\Admin\Workflows\WorkflowIndex;
use Illuminate\Support\Facades\Route;

// Public Home Route
Route::view('/', 'welcome')->name('home');

// Alias for standard Laravel Fortify dashboard redirect
Route::get('dashboard', Dashboard::class)->middleware(['auth', 'verified', 'admin'])->name('dashboard');

// Meta WhatsApp Cloud API Webhook Routes (CSRF exempt + Rate limited)
Route::prefix('webhook/whatsapp')->middleware('throttle:120,1')->group(function () {
    Route::get('/', [WhatsAppWebhookController::class, 'verify'])->name('webhook.whatsapp.verify');
    Route::post('/', [WhatsAppWebhookController::class, 'handle'])->name('webhook.whatsapp.handle');
});

// Sarvam AI Voice Telephony Webhook Routes (CSRF exempt + Rate limited)
Route::prefix('webhook/sarvam/calls')->middleware('throttle:120,1')->group(function () {
    Route::get('/', [SarvamWebhookController::class, 'verify'])->name('webhook.sarvam.verify');
    Route::post('/', [SarvamWebhookController::class, 'handle'])->name('webhook.sarvam.calls');
});

// Admin Livewire SaaS Routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('livewire/admin')->name('admin.')->group(function () {
    // 1. Dashboard
    Route::get('dashboard', Dashboard::class)->name('dashboard');

    // 2. Businesses (Tenants) CRUD
    Route::prefix('businesses')->name('businesses.')->group(function () {
        Route::get('/', BusinessIndex::class)->name('index');
        Route::get('create', BusinessCreate::class)->name('create');
        Route::get('{business}', BusinessShow::class)->name('show');
        Route::get('{business}/edit', BusinessEdit::class)->name('edit');
    });

    // 3. Contacts & Leads CRUD
    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/', ContactIndex::class)->name('index');
        Route::get('create', ContactCreate::class)->name('create');
        Route::get('{contact}', ContactShow::class)->name('show');
        Route::get('{contact}/edit', ContactEdit::class)->name('edit');
    });

    // 4. WhatsApp Conversations & Inbox
    Route::prefix('conversations')->name('conversations.')->group(function () {
        Route::get('/', ConversationIndex::class)->name('index');
    });

    // 5. Voice Calls CRUD
    Route::prefix('calls')->name('calls.')->group(function () {
        Route::get('/', CallIndex::class)->name('index');
        Route::get('create', CallCreate::class)->name('create');
        Route::get('{call}', CallShow::class)->name('show');
    });

    // 5b. Sarvam Phone Numbers Management
    Route::get('phone-numbers', PhoneNumbersIndex::class)->name('phone-numbers.index');

    // 5c. Voice Calling Settings & Compliance
    Route::get('calling-settings', CallingSettings::class)->name('calling-settings.index');

    // 6. AI Agents CRUD
    Route::prefix('ai-agents')->name('ai-agents.')->group(function () {
        Route::get('/', AgentIndex::class)->name('index');
        Route::get('create', AgentCreate::class)->name('create');
        Route::get('{agent}', AgentShow::class)->name('show');
        Route::get('{agent}/edit', AgentEdit::class)->name('edit');
    });

    // 6b. WhatsApp Interactive Messages Builder & Automation Rules
    Route::prefix('interactive-messages')->name('interactive.')->group(function () {
        Route::get('/', InteractiveIndex::class)->name('index');
        Route::get('create', InteractiveCreate::class)->name('create');
        Route::get('{template}', InteractiveShow::class)->name('show');
        Route::get('{template}/edit', InteractiveEdit::class)->name('edit');
    });

    // 7. Knowledge Bases CRUD
    Route::prefix('knowledge-bases')->name('knowledge-bases.')->group(function () {
        Route::get('/', KnowledgeBaseIndex::class)->name('index');
        Route::get('create', KnowledgeBaseCreate::class)->name('create');
        Route::get('{knowledgeBase}', KnowledgeBaseShow::class)->name('show');
        Route::get('{knowledgeBase}/edit', KnowledgeBaseEdit::class)->name('edit');
    });

    // 8. Business Routing Rules CRUD
    Route::prefix('routing')->name('routing.')->group(function () {
        Route::get('/', RoutingIndex::class)->name('index');
        Route::get('create', RoutingCreate::class)->name('create');
        Route::get('{rule}', RoutingShow::class)->name('show');
        Route::get('{rule}/edit', RoutingEdit::class)->name('edit');
    });

    // 9. Workflows Builder
    Route::prefix('workflows')->name('workflows.')->group(function () {
        Route::get('/', WorkflowIndex::class)->name('index');
    });

    // 9b. Voice Calling Workflows
    Route::get('calling-workflows', CallingWorkflowIndex::class)->name('calling-workflows.index');

    // 10. Campaigns
    Route::prefix('campaigns')->name('campaigns.')->group(function () {
        Route::get('/', CampaignIndex::class)->name('index');
    });

    // 11. Follow-ups
    Route::prefix('follow-ups')->name('follow-ups.')->group(function () {
        Route::get('/', FollowUpIndex::class)->name('index');
    });

    // 12. Analytics
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', AnalyticsIndex::class)->name('index');
    });

    // 13. Integrations & API Settings
    Route::prefix('integrations')->name('integrations.')->group(function () {
        Route::get('/', IntegrationIndex::class)->name('index');
    });

    // 14. Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', SettingsIndex::class)->name('index');
    });
});

require __DIR__.'/settings.php';

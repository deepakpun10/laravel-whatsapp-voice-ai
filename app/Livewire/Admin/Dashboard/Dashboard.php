<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\Business;
use App\Models\Call;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\TenantContext;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Dashboard', 'breadcrumb' => 'Workspace / Dashboard'])]
class Dashboard extends Component
{
    public function render()
    {
        $activeBusinessId = TenantContext::getActiveBusinessId();

        $contactsQuery = Contact::when($activeBusinessId, fn ($q) => $q->where('business_id', $activeBusinessId));
        $conversationsQuery = Conversation::when($activeBusinessId, fn ($q) => $q->where('business_id', $activeBusinessId));
        $callsQuery = Call::when($activeBusinessId, fn ($q) => $q->where('business_id', $activeBusinessId));
        $messagesQuery = Message::whereHas('conversation', fn ($q) => $q->when($activeBusinessId, fn ($sq) => $sq->where('business_id', $activeBusinessId)));

        $totalConversations = $conversationsQuery->count();
        $aiRepliesSent = (clone $messagesQuery)->where('sender_type', 'ai')->count();
        $callsHandled = $callsQuery->count();
        $qualifiedLeads = (clone $contactsQuery)->where('lead_status', 'qualified')->count();

        $recentConversations = Conversation::with(['contact', 'business'])
            ->when($activeBusinessId, fn ($q) => $q->where('business_id', $activeBusinessId))
            ->latest('last_message_at')
            ->take(5)
            ->get();

        $recentCalls = Call::with(['contact', 'business', 'aiAgent'])
            ->when($activeBusinessId, fn ($q) => $q->where('business_id', $activeBusinessId))
            ->latest()
            ->take(5)
            ->get();

        $businesses = Business::withCount(['contacts', 'conversations'])
            ->where('status', 'active')
            ->get();

        return view('livewire.admin.dashboard.dashboard', [
            'totalConversations' => $totalConversations,
            'aiRepliesSent' => $aiRepliesSent,
            'callsHandled' => $callsHandled,
            'qualifiedLeads' => $qualifiedLeads,
            'recentConversations' => $recentConversations,
            'recentCalls' => $recentCalls,
            'businesses' => $businesses,
            'activeBusiness' => TenantContext::getActiveBusiness(),
        ]);
    }
}

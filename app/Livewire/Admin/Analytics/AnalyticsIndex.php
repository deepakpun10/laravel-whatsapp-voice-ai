<?php

namespace App\Livewire\Admin\Analytics;

use App\Models\Business;
use App\Models\Call;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\TenantContext;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Analytics', 'breadcrumb' => 'Workspace / Analytics'])]
class AnalyticsIndex extends Component
{
    public function render()
    {
        $activeBusinessId = TenantContext::getActiveBusinessId();

        $totalContacts = Contact::when($activeBusinessId, fn ($q) => $q->where('business_id', $activeBusinessId))->count();
        $totalConversations = Conversation::when($activeBusinessId, fn ($q) => $q->where('business_id', $activeBusinessId))->count();
        $totalCalls = Call::when($activeBusinessId, fn ($q) => $q->where('business_id', $activeBusinessId))->count();

        $aiMessages = Message::where('sender_type', 'ai')
            ->whereHas('conversation', fn ($q) => $q->when($activeBusinessId, fn ($sq) => $sq->where('business_id', $activeBusinessId)))
            ->count();

        $humanMessages = Message::where('sender_type', 'human')
            ->whereHas('conversation', fn ($q) => $q->when($activeBusinessId, fn ($sq) => $sq->where('business_id', $activeBusinessId)))
            ->count();

        $totalReplies = $aiMessages + $humanMessages;
        $aiResolutionRate = $totalReplies > 0 ? round(($aiMessages / $totalReplies) * 100, 1) : 0;
        $humanHandoverRate = $totalReplies > 0 ? round(($humanMessages / $totalReplies) * 100, 1) : 0;

        $qualifiedLeads = Contact::when($activeBusinessId, fn ($q) => $q->where('business_id', $activeBusinessId))
            ->where('lead_status', 'qualified')->count();
        $convertedLeads = Contact::when($activeBusinessId, fn ($q) => $q->where('business_id', $activeBusinessId))
            ->where('lead_status', 'converted')->count();

        $businessPerformance = Business::withCount(['contacts', 'conversations'])
            ->where('status', 'active')
            ->get();

        return view('livewire.admin.analytics.index', [
            'totalContacts' => $totalContacts,
            'totalConversations' => $totalConversations,
            'totalCalls' => $totalCalls,
            'aiResolutionRate' => $aiResolutionRate,
            'humanHandoverRate' => $humanHandoverRate,
            'qualifiedLeads' => $qualifiedLeads,
            'convertedLeads' => $convertedLeads,
            'businessPerformance' => $businessPerformance,
        ]);
    }
}

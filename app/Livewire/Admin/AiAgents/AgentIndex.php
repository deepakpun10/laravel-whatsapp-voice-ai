<?php

namespace App\Livewire\Admin\AiAgents;

use App\Models\AiAgent;
use App\Models\Business;
use App\Services\TenantContext;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin', ['title' => 'AI Agents', 'breadcrumb' => 'Workspace / AI Agents'])]
class AgentIndex extends Component
{
    use WithPagination;

    public string $businessFilter = 'all';

    public string $channelFilter = 'all'; // all, whatsapp_chat, voice_calling

    public function mount(): void
    {
        $activeId = TenantContext::getActiveBusinessId();
        if ($activeId) {
            $this->businessFilter = (string) $activeId;
        }
    }

    public function setChannelFilter(string $channel): void
    {
        $this->channelFilter = $channel;
        $this->resetPage();
    }

    public function toggleAgent(int $id): void
    {
        $agent = AiAgent::findOrFail($id);
        $agent->is_active = ! $agent->is_active;
        $agent->save();

        $this->dispatch('toast', message: "Agent {$agent->name} is now ".($agent->is_active ? 'Active' : 'Paused').'.');
    }

    public function deleteAgent(int $id): void
    {
        $agent = AiAgent::findOrFail($id);
        $agent->delete();
        $this->dispatch('toast', message: "Agent {$agent->name} deleted.");
    }

    public function render()
    {
        $agents = AiAgent::with(['business', 'apiConnection', 'knowledgeBases'])
            ->when($this->businessFilter !== 'all', fn ($q) => $q->where('business_id', $this->businessFilter))
            ->when($this->channelFilter !== 'all', fn ($q) => $q->where('agent_type', $this->channelFilter))
            ->latest()
            ->paginate(12);

        $allAgentsForCompanions = AiAgent::select('id', 'business_id', 'name', 'agent_type', 'is_active')->get();

        $totalCount = AiAgent::count();
        $chatCount = AiAgent::where('agent_type', 'whatsapp_chat')->count();
        $voiceCount = AiAgent::where('agent_type', 'voice_calling')->count();

        return view('livewire.admin.ai-agents.index', [
            'agents' => $agents,
            'businesses' => Business::where('status', 'active')->get(),
            'companionAgents' => $allAgentsForCompanions,
            'totalCount' => $totalCount,
            'chatCount' => $chatCount,
            'voiceCount' => $voiceCount,
        ]);
    }
}

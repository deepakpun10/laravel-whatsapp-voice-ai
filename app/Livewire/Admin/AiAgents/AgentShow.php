<?php

namespace App\Livewire\Admin\AiAgents;

use App\Models\AiAgent;
use App\Services\AI\AiProviderService;
use App\Services\Knowledge\RagRetrievalService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'AI Agent Details & Playground', 'breadcrumb' => 'Workspace / AI Agents / Playground'])]
class AgentShow extends Component
{
    public AiAgent $agent;

    // Playground state
    public string $testQuery = '';

    public ?array $testResult = null;

    public function mount(AiAgent $agent): void
    {
        $this->agent = $agent->load(['business', 'apiConnection', 'fallbackConnection', 'knowledgeBases']);
    }

    public function testAgent(AiProviderService $aiService, RagRetrievalService $ragService): void
    {
        if (empty(trim($this->testQuery))) {
            return;
        }

        // 1. Strictly scoped RAG retrieval
        $chunks = $ragService->retrieve(
            $this->testQuery,
            $this->agent->business_id,
            $this->agent->id,
            3
        );

        // 2. Generate actual AI reply
        $replyResult = $aiService->generateAgentReply(
            $this->agent,
            $this->testQuery,
            $chunks
        );

        $this->testResult = [
            'query' => $this->testQuery,
            'reply' => $replyResult['reply'] ?? 'No reply generated.',
            'provider' => $replyResult['provider'] ?? 'Fallback Engine',
            'retrieved_chunks' => $chunks,
            'status' => $replyResult['status'] ?? ($replyResult['success'] ? 'success' : 'failed'),
        ];
    }

    public function toggle(): void
    {
        $this->agent->is_active = ! $this->agent->is_active;
        $this->agent->save();

        $this->dispatch('toast', message: 'Agent is now '.($this->agent->is_active ? 'Active' : 'Paused').'.');
    }

    public function render()
    {
        return view('livewire.admin.ai-agents.show');
    }
}

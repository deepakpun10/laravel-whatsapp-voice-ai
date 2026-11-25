<?php

namespace App\Livewire\Admin\AiAgents;

use App\Models\AiAgent;
use App\Models\ApiConnection;
use App\Models\KnowledgeBase;
use App\Services\Telephony\SarvamTelephonyService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Edit AI Agent', 'breadcrumb' => 'Workspace / AI Agents / Edit'])]
class AgentEdit extends Component
{
    public AiAgent $agent;

    public int $business_id;

    public ?int $api_connection_id = null;

    public ?int $fallback_connection_id = null;

    public string $name = '';

    public string $agent_type = 'whatsapp_chat';

    public string $description = '';

    public string $model = 'gpt-4o-mini';

    public string $system_instructions = '';

    public string $personality_tone = 'professional';

    public string $unknown_query_policy = 'ask_clarification';

    public string $human_handover_policy = 'notify_and_pause';

    // Voice specific
    public string $sarvam_agent_id = '';

    public string $voice_language = 'hi-IN';

    public string $voice_gender = 'female';

    public array $sarvamVoiceAgents = [];

    public bool $isCustomSarvamAgent = false;

    public ?string $sarvamFetchMessage = null;

    public ?string $sarvamFetchError = null;

    public array $assigned_kb_ids = [];

    public function mount(AiAgent $agent): void
    {
        $this->agent = $agent;
        $this->business_id = $agent->business_id;
        $this->api_connection_id = $agent->api_connection_id;
        $this->fallback_connection_id = $agent->fallback_connection_id;
        $this->name = $agent->name;
        $this->agent_type = $agent->agent_type ?? 'whatsapp_chat';

        // Auto-detect voice calling agent if model or name indicates telephony
        if ($this->agent_type !== 'voice_calling' && (str_contains(strtolower($agent->model), 'sarvam') || str_contains(strtolower($agent->name), 'voice'))) {
            $this->agent_type = 'voice_calling';
        }

        $this->description = $agent->description ?? '';
        $this->model = $agent->model;
        $this->system_instructions = $agent->system_instructions ?? '';
        $this->personality_tone = $agent->personality_tone;
        $this->unknown_query_policy = $agent->unknown_query_policy;
        $this->human_handover_policy = $agent->human_handover_policy;
        $this->assigned_kb_ids = $agent->knowledgeBases()->pluck('knowledge_bases.id')->toArray();

        $vCfg = $agent->voice_config ?? [];
        $this->sarvam_agent_id = $vCfg['sarvam_agent_id'] ?? ($this->agent_type === 'voice_calling' ? 'sarvam-telephony-agent' : '');
        $this->voice_language = $vCfg['voice_language'] ?? 'hi-IN';
        $this->voice_gender = $vCfg['voice_gender'] ?? 'female';

        $this->loadDefaultSarvamAgents();
    }

    public function loadDefaultSarvamAgents(): void
    {
        $this->sarvamVoiceAgents = [
            [
                'id' => 'sarvam-telephony-agent',
                'name' => 'Sarvam Conversational Telephony Voice Agent (Standard)',
                'language' => 'hi-IN',
            ],
            [
                'id' => 'sarvam-telephony-v1',
                'name' => 'Sarvam Telephony v1 (Low Latency Inbound/Outbound)',
                'language' => 'hi-IN',
            ],
            [
                'id' => 'bulbul:v1',
                'name' => 'Sarvam Bulbul v1 (High-Fidelity Indic Speech)',
                'language' => 'hi-IN',
            ],
        ];
    }

    public function fetchSarvamVoiceAgents(): void
    {
        $this->sarvamFetchMessage = null;
        $this->sarvamFetchError = null;

        $conn = ApiConnection::where('provider_name', 'sarvam')
            ->orWhere('provider_type', 'voice')
            ->first();

        if (! $conn || empty($conn->api_key)) {
            $this->sarvamFetchError = 'No Sarvam API Key found. Go to Integrations to save your Sarvam Workspace API Key, or select a default Sarvam agent below.';

            return;
        }

        $telephony = app(SarvamTelephonyService::class);
        $res = $telephony->fetchVoiceAgents(
            $conn->api_key,
            $conn->base_url,
            $conn->custom_headers['workspace_id'] ?? null,
            $conn->organization_id
        );

        if ($res['success'] && ! empty($res['agents'])) {
            $this->sarvamVoiceAgents = $res['agents'];
            $this->sarvamFetchMessage = '✓ '.count($res['agents']).' voice agents auto-fetched from your Sarvam workspace!';

            if (empty($this->sarvam_agent_id)) {
                $this->sarvam_agent_id = $res['agents'][0]['id'] ?? 'sarvam-telephony-agent';
            }

            $this->dispatch('toast', message: $this->sarvamFetchMessage);
        } else {
            $this->sarvamFetchError = 'Sarvam API: '.($res['error'] ?? 'Could not fetch agents. Using default templates.');
        }
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'agent_type' => ['required', 'in:whatsapp_chat,voice_calling'],
            'model' => ['required', 'string', 'max:100'],
            'system_instructions' => ['nullable', 'string', 'max:3000'],
            'personality_tone' => ['required', 'string'],
            'unknown_query_policy' => ['required', 'string'],
        ];
    }

    public function update()
    {
        $this->validate();

        $voiceConfig = null;
        if ($this->agent_type === 'voice_calling') {
            $voiceConfig = [
                'provider' => 'sarvam',
                'sarvam_agent_id' => $this->sarvam_agent_id,
                'voice_language' => $this->voice_language,
                'voice_gender' => $this->voice_gender,
            ];
        }

        $this->agent->update([
            'api_connection_id' => $this->api_connection_id,
            'fallback_connection_id' => $this->fallback_connection_id,
            'name' => $this->name,
            'agent_type' => $this->agent_type,
            'description' => $this->description,
            'model' => $this->model,
            'system_instructions' => $this->system_instructions,
            'personality_tone' => $this->personality_tone,
            'unknown_query_policy' => $this->unknown_query_policy,
            'human_handover_policy' => $this->human_handover_policy,
            'voice_config' => $voiceConfig,
        ]);

        if ($this->agent_type === 'whatsapp_chat') {
            $this->agent->knowledgeBases()->sync($this->assigned_kb_ids);
        }

        session()->flash('success', "AI Agent '{$this->agent->name}' updated successfully.");

        return $this->redirect(route('admin.ai-agents.show', $this->agent), navigate: true);
    }

    public function render()
    {
        $eligibleKbs = KnowledgeBase::where('business_id', $this->business_id)->get();
        $aiConnections = ApiConnection::all();

        return view('livewire.admin.ai-agents.edit', [
            'eligibleKbs' => $eligibleKbs,
            'aiConnections' => $aiConnections,
        ]);
    }
}

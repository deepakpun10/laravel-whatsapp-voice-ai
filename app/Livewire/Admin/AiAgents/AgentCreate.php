<?php

namespace App\Livewire\Admin\AiAgents;

use App\Models\AiAgent;
use App\Models\ApiConnection;
use App\Models\Business;
use App\Models\KnowledgeBase;
use App\Services\Telephony\SarvamTelephonyService;
use App\Services\TenantContext;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Create AI Agent', 'breadcrumb' => 'Workspace / AI Agents / Create'])]
class AgentCreate extends Component
{
    public ?int $business_id = null;

    public ?int $api_connection_id = null;

    public ?int $fallback_connection_id = null;

    public string $name = '';

    public string $agent_type = 'whatsapp_chat'; // whatsapp_chat, voice_calling

    public string $description = '';

    public string $model = 'gpt-4o-mini';

    public string $system_instructions = '';

    public string $personality_tone = 'professional';

    public string $unknown_query_policy = 'ask_clarification';

    public string $human_handover_policy = 'notify_and_pause';

    // Voice specific settings for Sarvam AI telephony
    public string $sarvam_agent_id = 'sarvam-telephony-agent';

    public string $voice_language = 'hi-IN';

    public string $voice_gender = 'female';

    public array $sarvamVoiceAgents = [];

    public bool $isCustomSarvamAgent = false;

    public ?string $sarvamFetchMessage = null;

    public ?string $sarvamFetchError = null;

    public array $assigned_kb_ids = [];

    public function mount(): void
    {
        $activeId = TenantContext::getActiveBusinessId();
        if ($activeId) {
            $this->business_id = $activeId;
        } else {
            $this->business_id = Business::first()?->id;
        }

        $defaultConn = ApiConnection::where('provider_type', 'ai')->first();
        $this->api_connection_id = $defaultConn?->id;

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

            if (empty($this->sarvam_agent_id) || $this->sarvam_agent_id === 'sarvam-telephony-agent') {
                $this->sarvam_agent_id = $res['agents'][0]['id'] ?? 'sarvam-telephony-agent';
            }

            $this->dispatch('toast', message: $this->sarvamFetchMessage);
        } else {
            $this->sarvamFetchError = 'Sarvam API: '.($res['error'] ?? 'Could not fetch agents. Using default templates.');
        }
    }

    public function updatedAgentType(string $val): void
    {
        if ($val === 'voice_calling') {
            $this->model = 'sarvam-telephony-v1';
            $voiceConn = ApiConnection::where('provider_type', 'voice')->orWhere('provider_name', 'sarvam')->first();
            if ($voiceConn) {
                $this->api_connection_id = $voiceConn->id;
            }
            if (empty($this->name)) {
                $this->name = 'Voice Telephony Calling Agent (Sarvam)';
            }
        } else {
            $this->model = 'gpt-4o-mini';
            $aiConn = ApiConnection::where('provider_type', 'ai')->first();
            if ($aiConn) {
                $this->api_connection_id = $aiConn->id;
            }
        }
    }

    protected function rules(): array
    {
        return [
            'business_id' => ['required', 'exists:businesses,id'],
            'agent_type' => ['required', 'in:whatsapp_chat,voice_calling'],
            'name' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:100'],
            'system_instructions' => ['nullable', 'string', 'max:3000'],
            'personality_tone' => ['required', 'string'],
            'unknown_query_policy' => ['required', 'string'],
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        $voiceConfig = null;
        if ($this->agent_type === 'voice_calling') {
            $voiceConfig = [
                'provider' => 'sarvam',
                'sarvam_agent_id' => $this->sarvam_agent_id,
                'voice_language' => $this->voice_language,
                'voice_gender' => $this->voice_gender,
            ];
        }

        $agent = AiAgent::create([
            'business_id' => $this->business_id,
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
            'is_active' => true,
        ]);

        if (! empty($this->assigned_kb_ids) && $this->agent_type === 'whatsapp_chat') {
            $agent->knowledgeBases()->sync($this->assigned_kb_ids);
        }

        session()->flash('success', "AI Agent '{$agent->name}' created successfully.");

        return $this->redirect(route('admin.ai-agents.show', $agent), navigate: true);
    }

    public function render()
    {
        $eligibleKbs = KnowledgeBase::where('business_id', $this->business_id)->get();
        $aiConnections = ApiConnection::all();

        return view('livewire.admin.ai-agents.create', [
            'businesses' => Business::where('status', 'active')->orderBy('name')->get(),
            'eligibleKbs' => $eligibleKbs,
            'aiConnections' => $aiConnections,
        ]);
    }
}

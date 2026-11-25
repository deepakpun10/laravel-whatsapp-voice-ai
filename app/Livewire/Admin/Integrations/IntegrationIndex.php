<?php

namespace App\Livewire\Admin\Integrations;

use App\Models\AiAgent;
use App\Models\ApiConnection;
use App\Models\Business;
use App\Models\SarvamPhoneNumber;
use App\Models\WhatsappAccount;
use App\Services\AI\AiProviderService;
use App\Services\Telephony\SarvamTelephonyService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Integrations & API Settings', 'breadcrumb' => 'Workspace / Integrations'])]
class IntegrationIndex extends Component
{
    // Preset model definitions per provider
    public static array $presetModels = [
        'groq' => [
            'llama-3.3-70b-versatile' => 'Llama 3.3 70B Versatile (Recommended, Fast & Smart)',
            'llama-3.1-8b-instant' => 'Llama 3.1 8B Instant (Ultra-Fast, Low Latency)',
            'llama-3.3-70b-specdec' => 'Llama 3.3 70B Speculative Decoding',
            'llama3-70b-8192' => 'Llama 3 70B (8k Context)',
            'llama3-8b-8192' => 'Llama 3 8B (8k Context)',
            'mixtral-8x7b-32768' => 'Mixtral 8x7B (32k Context)',
            'gemma2-9b-it' => 'Google Gemma 2 9B IT',
        ],
        'openai' => [
            'gpt-4o' => 'GPT-4o (Omni Flagship)',
            'gpt-4o-mini' => 'GPT-4o Mini (Fast & Cost Efficient)',
            'o1' => 'o1 (Reasoning)',
            'o1-mini' => 'o1 Mini (Fast Reasoning)',
            'gpt-4-turbo' => 'GPT-4 Turbo',
            'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
        ],
        'sarvam' => [
            'sarvam-telephony-agent' => 'Sarvam Telephony Conversational Voice Agent',
            'sarvam-2b' => 'Sarvam 2B (Indic Regional Language LLM)',
            'bulbul:v1' => 'Bulbul v1 (High-Fidelity Indic TTS)',
            'saaras:v1' => 'Saaras v1 (Indic STT Speech-to-Text)',
        ],
        'anthropic' => [
            'claude-3-5-sonnet-20241022' => 'Claude 3.5 Sonnet (Latest Flagship)',
            'claude-3-5-haiku-20241022' => 'Claude 3.5 Haiku (Ultra-Fast)',
            'claude-3-opus-20240229' => 'Claude 3 Opus',
        ],
    ];

    // Form state for creating API Connection
    public string $name = '';

    public string $provider_type = 'ai';

    public string $provider_name = 'groq';

    public string $base_url = 'https://api.groq.com/openai/v1';

    public string $api_key = '';

    public string $model_or_agent_id = 'llama-3.3-70b-versatile';

    public string $priority = 'primary';

    public ?int $business_id = null;

    // Quick preset selector
    public string $selectedPreset = 'groq';

    // Sarvam Voice Platform Specific Credentials & Config
    public string $sarvam_org_id = '';

    public string $sarvam_workspace_id = '';

    public string $sarvam_agent_id = 'sarvam-telephony-agent';

    public string $sarvam_agent_version = 'v1';

    public string $sarvam_connection_id = '';

    public string $sarvam_phone_number = '';

    public string $sarvam_inbound_deployment_id = '';

    // Dynamic model management
    public array $availableModels = [];

    public bool $isCustomModel = false;

    public string $custom_model_id = '';

    public ?string $modelsFetchStatus = null;

    public ?string $modelsFetchError = null;

    public bool $isFetchingModels = false;

    // WhatsApp Configuration State
    public string $wa_phone_number_id = '';

    public string $wa_waba_id = '';

    public string $wa_access_token = '';

    public string $wa_verify_token = '';

    public function mount(): void
    {
        $this->applyPreset('groq');

        $sharedLine = WhatsappAccount::where('is_shared', true)->first();
        if ($sharedLine) {
            $this->wa_phone_number_id = $sharedLine->phone_number_id ?? '';
            $this->wa_waba_id = $sharedLine->waba_id ?? '';
            $this->wa_verify_token = $sharedLine->webhook_verify_token ?? config('services.whatsapp.webhook_verify_token', 'd29cfa32157646f5f478cf36a87528d8f59f');
        }
    }

    public function updatedSelectedPreset(string $val): void
    {
        $this->applyPreset($val);
    }

    public function updatedApiKey(string $val): void
    {
        // Auto-fetch models if a plausible API key was entered
        if (strlen(trim($val)) >= 15) {
            $this->fetchModelsFromApi();
        }
    }

    public function applyPreset(string $preset): void
    {
        $this->selectedPreset = $preset;
        $this->isCustomModel = false;
        $this->custom_model_id = '';
        $this->modelsFetchStatus = null;
        $this->modelsFetchError = null;

        if ($preset === 'groq') {
            $this->provider_type = 'ai';
            $this->provider_name = 'groq';
            $this->name = 'Groq Cloud (Fast Auto-Reply)';
            $this->base_url = 'https://api.groq.com/openai/v1';
            $this->model_or_agent_id = 'llama-3.3-70b-versatile';
            $this->availableModels = self::$presetModels['groq'];
        } elseif ($preset === 'openai') {
            $this->provider_type = 'ai';
            $this->provider_name = 'openai';
            $this->name = 'OpenAI Primary';
            $this->base_url = 'https://api.openai.com/v1';
            $this->model_or_agent_id = 'gpt-4o-mini';
            $this->availableModels = self::$presetModels['openai'];
        } elseif ($preset === 'sarvam') {
            $this->provider_type = 'voice';
            $this->provider_name = 'sarvam';
            $this->name = 'Sarvam AI Voice Telephony';
            $this->base_url = 'https://api.sarvam.ai';
            $this->model_or_agent_id = 'sarvam-telephony-agent';
            $this->availableModels = self::$presetModels['sarvam'];
        } elseif ($preset === 'anthropic') {
            $this->provider_type = 'ai';
            $this->provider_name = 'anthropic';
            $this->name = 'Anthropic Claude';
            $this->base_url = 'https://api.anthropic.com/v1';
            $this->model_or_agent_id = 'claude-3-5-sonnet-20241022';
            $this->availableModels = self::$presetModels['anthropic'];
        } else {
            $this->availableModels = [];
        }

        // If an API key is already present, try to live-fetch for the new preset
        if (! empty(trim($this->api_key)) && strlen(trim($this->api_key)) >= 15) {
            $this->fetchModelsFromApi();
        }
    }

    public function fetchModelsFromApi(): void
    {
        $key = trim($this->api_key);
        if (empty($key)) {
            $this->modelsFetchError = 'Please paste your API Key first to auto-fetch live models.';
            $this->modelsFetchStatus = null;

            return;
        }

        $this->modelsFetchError = null;
        $this->modelsFetchStatus = 'Fetching live models from '.ucfirst($this->provider_name).'...';

        try {
            $baseUrl = rtrim($this->base_url ?: 'https://api.openai.com/v1', '/');

            if ($this->provider_name === 'sarvam') {
                $telephony = app(SarvamTelephonyService::class);
                $res = $telephony->fetchVoiceAgents($key, $this->base_url, $this->sarvam_workspace_id, $this->sarvam_org_id);

                if ($res['success'] && ! empty($res['agents'])) {
                    $fetched = [];
                    foreach ($res['agents'] as $agent) {
                        $fetched[$agent['id']] = $agent['name'].' ('.$agent['id'].')';
                    }
                    $this->availableModels = $fetched;
                    $this->modelsFetchStatus = '✓ '.count($fetched).' Sarvam Voice Agents / models auto-fetched!';
                    $this->modelsFetchError = null;

                    if (! isset($this->availableModels[$this->model_or_agent_id])) {
                        $this->model_or_agent_id = array_key_first($this->availableModels);
                    }

                    $this->dispatch('toast', message: $this->modelsFetchStatus);

                    return;
                }

                $this->modelsFetchError = 'Could not fetch Sarvam Voice Agents: '.($res['error'] ?? 'No agents returned');
                $this->modelsFetchStatus = null;

                return;
            }

            if ($this->provider_name === 'anthropic') {
                $response = Http::withHeaders([
                    'x-api-key' => $key,
                    'anthropic-version' => '2023-06-01',
                ])->timeout(8)->get('https://api.anthropic.com/v1/models');
            } else {
                $response = Http::withToken($key)
                    ->timeout(8)
                    ->get("{$baseUrl}/models");
            }

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];
                $fetched = [];

                foreach ($data as $item) {
                    $id = $item['id'] ?? null;
                    if ($id) {
                        $fetched[$id] = $id;
                    }
                }

                if (! empty($fetched)) {
                    ksort($fetched);
                    $this->availableModels = $fetched;
                    $this->modelsFetchStatus = '✓ '.count($fetched).' live models auto-fetched from '.ucfirst($this->provider_name).' API!';
                    $this->modelsFetchError = null;

                    if (! isset($this->availableModels[$this->model_or_agent_id])) {
                        if (isset($this->availableModels['llama-3.3-70b-versatile'])) {
                            $this->model_or_agent_id = 'llama-3.3-70b-versatile';
                        } elseif (isset($this->availableModels['gpt-4o-mini'])) {
                            $this->model_or_agent_id = 'gpt-4o-mini';
                        } else {
                            $this->model_or_agent_id = array_key_first($this->availableModels);
                        }
                    }

                    $this->dispatch('toast', message: $this->modelsFetchStatus);

                    return;
                }
            }

            $rawError = $response->json()['error']['message'] ?? $response->body();
            $this->modelsFetchError = 'Could not fetch live models: '.Str::limit($rawError, 90);
            $this->modelsFetchStatus = null;
        } catch (\Throwable $e) {
            $this->modelsFetchError = 'Connection error: '.Str::limit($e->getMessage(), 90);
            $this->modelsFetchStatus = null;
        }
    }

    public function updatedProviderType(string $val): void
    {
        if ($val === 'voice') {
            $this->applyPreset('sarvam');
        } elseif ($val === 'ai') {
            $this->applyPreset('groq');
        }
    }

    public function saveWhatsAppSettings(): void
    {
        $account = WhatsappAccount::firstOrCreate(
            ['is_shared' => true],
            ['name' => 'Main Shared WhatsApp Line']
        );

        $account->update([
            'phone_number_id' => $this->wa_phone_number_id,
            'waba_id' => $this->wa_waba_id,
            'access_token' => $this->wa_access_token ?: $account->access_token,
            'webhook_verify_token' => $this->wa_verify_token,
            'status' => (! empty($this->wa_phone_number_id) && ! empty($this->wa_access_token)) ? 'connected' : 'disconnected',
        ]);

        $this->dispatch('toast', message: 'WhatsApp Cloud API settings saved.');
    }

    public function saveConnection(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'provider_type' => 'required|in:ai,voice,telephony,vector,notifications',
            'provider_name' => 'required|string|max:100',
            'api_key' => 'nullable|string|max:1000',
        ]);

        $modelId = ($this->isCustomModel && ! empty(trim($this->custom_model_id)))
            ? trim($this->custom_model_id)
            : $this->model_or_agent_id;

        $customHeaders = null;
        $orgId = null;

        if ($this->provider_name === 'sarvam') {
            $modelId = $this->sarvam_agent_id ?: $modelId;
            $orgId = $this->sarvam_org_id ?: null;
            $customHeaders = [
                'workspace_id' => $this->sarvam_workspace_id ?: null,
                'agent_version' => $this->sarvam_agent_version ?: 'v1',
                'connection_id' => $this->sarvam_connection_id ?: null,
                'phone_number' => $this->sarvam_phone_number ?: null,
                'inbound_deployment_id' => $this->sarvam_inbound_deployment_id ?: null,
            ];
        }

        $conn = ApiConnection::create([
            'business_id' => $this->business_id,
            'name' => $this->name,
            'provider_type' => $this->provider_type,
            'provider_name' => $this->provider_name,
            'base_url' => $this->base_url,
            'api_key' => $this->api_key,
            'model_or_agent_id' => $modelId,
            'organization_id' => $orgId,
            'custom_headers' => $customHeaders,
            'priority' => $this->priority,
        ]);

        // Auto-register Sarvam Phone Number if provided
        if ($this->provider_name === 'sarvam' && ! empty($this->sarvam_phone_number)) {
            $cleaned = preg_replace('/[^0-9+]/', '', $this->sarvam_phone_number);
            SarvamPhoneNumber::updateOrCreate(
                ['phone_number' => $cleaned],
                [
                    'business_id' => $this->business_id,
                    'api_connection_id' => $conn->id,
                    'label' => 'Primary Sarvam Telephony Number',
                    'status' => 'active',
                    'inbound_enabled' => true,
                    'outbound_enabled' => true,
                    'inbound_deployment_id' => $this->sarvam_inbound_deployment_id ?: null,
                    'telephony_connection_id' => $this->sarvam_connection_id ?: null,
                ]
            );
        }

        // Auto-link AI connection to AI Agents so WhatsApp auto-reply works instantly
        if ($this->provider_type === 'ai') {
            $agents = AiAgent::where('agent_type', 'whatsapp_chat')
                ->when($this->business_id, fn ($q) => $q->where('business_id', $this->business_id))
                ->get();
            if ($agents->isNotEmpty()) {
                foreach ($agents as $agent) {
                    if (empty($agent->api_connection_id) || $this->priority === 'primary') {
                        $agent->update(['api_connection_id' => $conn->id]);
                    }
                }
            } else {
                // If no agent exists yet, create default WhatsApp AI agent for the active business
                $targetBusiness = $this->business_id ? Business::find($this->business_id) : Business::first();
                if ($targetBusiness) {
                    AiAgent::create([
                        'business_id' => $targetBusiness->id,
                        'api_connection_id' => $conn->id,
                        'name' => 'WhatsApp Sales Assistant',
                        'agent_type' => 'whatsapp_chat',
                        'model' => $this->model_or_agent_id ?: 'llama-3.3-70b-versatile',
                        'system_instructions' => "You are an intelligent WhatsApp sales assistant for {$targetBusiness->name}. Help customers with enquiries, pricing, and orders professionally.",
                        'personality_tone' => 'helpful, prompt and professional',
                        'is_active' => true,
                    ]);
                }
            }
        } elseif ($this->provider_type === 'voice') {
            $voiceAgents = AiAgent::where('agent_type', 'voice_calling')
                ->when($this->business_id, fn ($q) => $q->where('business_id', $this->business_id))
                ->get();
            foreach ($voiceAgents as $vAgent) {
                $vAgent->update(['api_connection_id' => $conn->id]);
            }
        }

        $savedName = $this->name;
        $this->reset(['name', 'api_key', 'sarvam_phone_number', 'sarvam_org_id', 'sarvam_workspace_id']);
        $this->applyPreset($this->selectedPreset);

        $this->dispatch('toast', message: "✓ API Connection '{$savedName}' saved successfully.");
    }

    public function testConnection(int $id, AiProviderService $aiService, SarvamTelephonyService $sarvamService): void
    {
        $conn = ApiConnection::findOrFail($id);

        if ($conn->provider_name === 'sarvam' || $conn->provider_type === 'voice') {
            $customHeaders = $conn->custom_headers ?? [];
            $result = $sarvamService->testConnection(
                apiKey: $conn->api_key,
                baseUrl: $conn->base_url,
                workspaceId: $customHeaders['workspace_id'] ?? null,
                orgId: $conn->organization_id
            );

            $conn->update([
                'last_health_status' => $result['status'],
                'last_health_checked_at' => now(),
                'last_health_error' => $result['error'] ?? null,
            ]);

            $this->dispatch('toast', message: $result['success'] ? '✓ '.$result['message'] : '✕ Sarvam Health check failed: '.($result['error'] ?? 'API error'));
        } elseif ($conn->provider_type === 'ai') {
            $result = $aiService->testConnection($conn);
            $this->dispatch('toast', message: $result['success'] ? '✓ '.$result['message'] : '✕ Health check failed: '.($result['error'] ?? 'API error'));
        } else {
            $this->dispatch('toast', message: "Health check dispatched for {$conn->provider_name}.");
        }
    }

    public function deleteConnection(int $id): void
    {
        $conn = ApiConnection::findOrFail($id);
        $conn->delete();
        $this->dispatch('toast', message: 'API connection deleted.');
    }

    public function render()
    {
        $connections = ApiConnection::with('business')->latest()->get();
        $whatsappAccount = WhatsappAccount::where('is_shared', true)->first();

        return view('livewire.admin.integrations.index', [
            'connections' => $connections,
            'whatsappAccount' => $whatsappAccount,
            'businesses' => Business::where('status', 'active')->get(),
        ]);
    }
}

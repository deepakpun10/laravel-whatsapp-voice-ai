<div>
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:22px; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 style="font-size:24px; font-weight:800; margin:0 0 6px; color:#0f172a;">Integrations & API Settings</h1>
            <p style="margin:0; color:#64748b; font-size:13.5px;">Configure Meta WhatsApp, Sarvam AI, LLM providers, and telephony connections.</p>
        </div>
    </div>

    <!-- Integration Cards Grid matching prototype -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:18px; margin-bottom:24px;">
        <!-- Card 1: Meta WhatsApp -->
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03); display:flex; flex-direction:column; justify-content:space-between;">
            <div>
                <div style="font-size:32px; margin-bottom:8px;">🟢</div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                    <h3 style="font-size:16px; font-weight:700; margin:0; color:#0f172a;">Meta WhatsApp Cloud API</h3>
                    <span style="font-size:11px; font-weight:700; padding:2px 8px; border-radius:12px; {{ $whatsappAccount?->isConfigured() ? 'background:#ecfdf5; color:#16a34a;' : 'background:#fffbeb; color:#b45309;' }}">
                        {{ $whatsappAccount?->isConfigured() ? '● Configured' : '● Needs Setup' }}
                    </span>
                </div>
                <p style="color:#64748b; font-size:12.5px; line-height:1.5; margin:0 0 14px;">
                    Shared WhatsApp line, WABA ID, Phone Number ID, access token, and webhook synchronization.
                </p>
            </div>
            <div style="font-size:11.5px; color:#64748b; border-top:1px solid #f1f5f9; padding-top:10px;">
                Webhook Endpoint: <code style="background:#f1f5f9; padding:2px 6px; border-radius:4px;">/webhook/whatsapp</code>
            </div>
        </div>

        <!-- Card 2: Sarvam AI Voice -->
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03); display:flex; flex-direction:column; justify-content:space-between;">
            <div>
                <div style="font-size:32px; margin-bottom:8px;">🎙️</div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                    <h3 style="font-size:16px; font-weight:700; margin:0; color:#0f172a;">Sarvam AI (Voice)</h3>
                    <span style="font-size:11px; font-weight:700; padding:2px 8px; border-radius:12px; background:#edf3ff; color:#3568ca;">
                        Verified API Layer
                    </span>
                </div>
                <p style="color:#64748b; font-size:12.5px; line-height:1.5; margin:0 0 14px;">
                    Official Sarvam AI Indic voice synthesis, speech-to-text, and conversational agents in Hindi and regional languages.
                </p>
            </div>
            <div style="font-size:11.5px; color:#64748b; border-top:1px solid #f1f5f9; padding-top:10px;">
                Endpoint: <code style="background:#f1f5f9; padding:2px 6px; border-radius:4px;">https://api.sarvam.ai</code>
            </div>
        </div>

        <!-- Card 3: AI / LLM Providers -->
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03); display:flex; flex-direction:column; justify-content:space-between;">
            <div>
                <div style="font-size:32px; margin-bottom:8px;">🧠</div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                    <h3 style="font-size:16px; font-weight:700; margin:0; color:#0f172a;">Multi-Provider LLM</h3>
                    <span style="font-size:11px; font-weight:700; padding:2px 8px; border-radius:12px; background:#ecfdf5; color:#16a34a;">
                        OpenAI / Groq / Anthropic
                    </span>
                </div>
                <p style="color:#64748b; font-size:12.5px; line-height:1.5; margin:0 0 14px;">
                    Connect primary and fallback AI models per agent. Strict RAG injection ensures no cross-tenant price leaks.
                </p>
            </div>
            <div style="font-size:11.5px; color:#64748b; border-top:1px solid #f1f5f9; padding-top:10px;">
                Failover: Primary → Fallback → Safe Human Notice
            </div>
        </div>
    </div>

    <!-- WhatsApp Cloud API Settings Form Card -->
    <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:22px; box-shadow:0 6px 20px rgba(0,0,0,.03); margin-bottom:24px;">
        <h3 style="font-size:16px; font-weight:700; margin:0 0 12px; color:#0f172a;">Configure Meta WhatsApp Cloud API</h3>
        <p style="color:#64748b; font-size:12.5px; margin:0 0 16px;">
            Enter your Meta for Developers credentials for the shared WhatsApp Business line. Tokens are stored encrypted.
        </p>

        <form wire:submit="saveWhatsAppSettings">
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:16px; margin-bottom:16px;">
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                        WhatsApp Business Account ID (WABA ID)
                    </label>
                    <input
                        wire:model="wa_waba_id"
                        type="text"
                        placeholder="e.g. 10982347123908"
                        style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:8px 12px; font-size:13px;"
                    >
                </div>
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                        Phone Number ID
                    </label>
                    <input
                        wire:model="wa_phone_number_id"
                        type="text"
                        placeholder="e.g. 1045982347102"
                        style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:8px 12px; font-size:13px;"
                    >
                </div>
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                        Webhook Verify Token
                    </label>
                    <input
                        wire:model="wa_verify_token"
                        type="text"
                        style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:8px 12px; font-size:13px;"
                    >
                </div>
            </div>

            <div style="margin-bottom:18px;">
                <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                    Permanent System User Access Token
                </label>
                <input
                    wire:model="wa_access_token"
                    type="password"
                    placeholder="Enter Meta EAAB... access token (masked in UI)"
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:8px 12px; font-size:13px;"
                >
            </div>

            <button
                type="submit"
                style="background:#3978f6; color:white; border:0; border-radius:8px; padding:9px 18px; font-size:13px; font-weight:600; cursor:pointer;"
            >
                Save WhatsApp Settings
            </button>
        </form>
    </div>

    <!-- Provider Connections Table & Add Connection -->
    <div style="display:grid; grid-template-columns:1.5fr 1fr; gap:20px;">
        <!-- Left: Saved Connections -->
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
            <h3 style="font-size:16px; font-weight:700; margin:0 0 14px; color:#0f172a;">Configured API Connections</h3>
            <table style="width:100%; border-collapse:collapse; text-align:left; font-size:12.5px;">
                <thead>
                    <tr style="border-bottom:1px solid #e2e8f0; background:#f8fafc; color:#64748b;">
                        <th style="padding:10px;">NAME</th>
                        <th style="padding:10px;">TYPE</th>
                        <th style="padding:10px;">PRIORITY</th>
                        <th style="padding:10px;">HEALTH</th>
                        <th style="padding:10px; text-align:right;">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($connections as $conn)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:12px;">
                                <b style="color:#0f172a;">{{ $conn->name }}</b>
                                <div style="font-size:11px; color:#64748b;">{{ $conn->model_or_agent_id ?: $conn->provider_name }}</div>
                            </td>
                            <td style="padding:12px; text-transform:uppercase; font-size:11px; font-weight:700; color:#475569;">
                                {{ $conn->provider_type }}
                            </td>
                            <td style="padding:12px; text-transform:capitalize;">
                                {{ str_replace('_', ' ', $conn->priority) }}
                            </td>
                            <td style="padding:12px;">
                                <span style="font-size:11px; font-weight:700; padding:2px 7px; border-radius:10px; {{ $conn->last_health_status === 'healthy' ? 'background:#ecfdf5; color:#16a34a;' : 'background:#fef2f2; color:#dc2626;' }}">
                                    {{ ucfirst($conn->last_health_status) }}
                                </span>
                            </td>
                            <td style="padding:12px; text-align:right;">
                                <button
                                    wire:click="testConnection({{ $conn->id }})"
                                    style="padding:4px 8px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:5px; font-size:11px; font-weight:600; cursor:pointer;"
                                >
                                    Ping API
                                </button>
                                <button
                                    wire:confirm="Delete connection {{ $conn->name }}?"
                                    wire:click="deleteConnection({{ $conn->id }})"
                                    style="padding:4px 6px; border:1px solid #fee2e2; background:#fef2f2; color:#dc2626; border-radius:5px; font-size:11px; cursor:pointer; margin-left:4px;"
                                >
                                    ✕
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:26px; color:#94a3b8;">
                                No external API providers registered yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Right: Add API Connection Form -->
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <h3 style="font-size:16px; font-weight:700; margin:0; color:#0f172a;">Add API Connection</h3>
                <span style="font-size:11px; font-weight:700; padding:2px 8px; border-radius:10px; background:#f0fdf4; color:#15803d;">
                    {{ strtoupper($selectedPreset) }} Active
                </span>
            </div>

            <!-- Quick Preset Selector Pills -->
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:6px;">Select Provider Preset</label>
                <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:6px;">
                    <button
                        type="button"
                        wire:click="applyPreset('groq')"
                        style="padding:8px; border-radius:7px; font-size:11.5px; font-weight:700; cursor:pointer; text-align:left; transition:all .2s; border:1px solid {{ $selectedPreset === 'groq' ? '#22c55e' : '#e2e8f0' }}; background:{{ $selectedPreset === 'groq' ? '#f0fdf4' : '#f8fafc' }}; color:{{ $selectedPreset === 'groq' ? '#15803d' : '#334155' }};"
                    >
                        ⚡ <b>Groq Cloud</b>
                        <div style="font-size:10px; font-weight:400; color:#64748b;">Fast WhatsApp Auto-Reply</div>
                    </button>

                    <button
                        type="button"
                        wire:click="applyPreset('openai')"
                        style="padding:8px; border-radius:7px; font-size:11.5px; font-weight:700; cursor:pointer; text-align:left; transition:all .2s; border:1px solid {{ $selectedPreset === 'openai' ? '#3b82f6' : '#e2e8f0' }}; background:{{ $selectedPreset === 'openai' ? '#eff6ff' : '#f8fafc' }}; color:{{ $selectedPreset === 'openai' ? '#1d4ed8' : '#334155' }};"
                    >
                        🧠 <b>OpenAI</b>
                        <div style="font-size:10px; font-weight:400; color:#64748b;">GPT-4o & mini</div>
                    </button>

                    <button
                        type="button"
                        wire:click="applyPreset('sarvam')"
                        style="padding:8px; border-radius:7px; font-size:11.5px; font-weight:700; cursor:pointer; text-align:left; transition:all .2s; border:1px solid {{ $selectedPreset === 'sarvam' ? '#f97316' : '#e2e8f0' }}; background:{{ $selectedPreset === 'sarvam' ? '#fff7ed' : '#f8fafc' }}; color:{{ $selectedPreset === 'sarvam' ? '#c2410c' : '#334155' }};"
                    >
                        🎙️ <b>Sarvam AI</b>
                        <div style="font-size:10px; font-weight:400; color:#64748b;">Voice & Telephony</div>
                    </button>

                    <button
                        type="button"
                        wire:click="applyPreset('anthropic')"
                        style="padding:8px; border-radius:7px; font-size:11.5px; font-weight:700; cursor:pointer; text-align:left; transition:all .2s; border:1px solid {{ $selectedPreset === 'anthropic' ? '#8b5cf6' : '#e2e8f0' }}; background:{{ $selectedPreset === 'anthropic' ? '#faf5ff' : '#f8fafc' }}; color:{{ $selectedPreset === 'anthropic' ? '#6d28d9' : '#334155' }};"
                    >
                        🛡️ <b>Anthropic</b>
                        <div style="font-size:10px; font-weight:400; color:#64748b;">Claude 3.5 Sonnet</div>
                    </button>
                </div>
            </div>

            @if($selectedPreset === 'groq')
                <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:10px 12px; margin-bottom:14px; font-size:11.5px; color:#166534; line-height:1.45;">
                    ⚡ <b>Groq Selected:</b> Ultra-fast inference (~300 tokens/sec) for WhatsApp automated replies. Key starts with <code>gsk_</code>.
                    Get yours at <a href="https://console.groq.com/keys" target="_blank" style="color:#15803d; font-weight:700; text-decoration:underline;">console.groq.com/keys</a>.
                </div>
            @elseif($selectedPreset === 'sarvam')
                <div style="background:#fff7ed; border:1px solid #fed7aa; border-radius:8px; padding:10px 12px; margin-bottom:14px; font-size:11.5px; color:#9a3412; line-height:1.45;">
                    🎙️ <b>Sarvam AI Selected:</b> Inbound and Outbound Indic voice agents, phone numbers, and telephony.
                    Enter your Sarvam Workspace API Key below to auto-fetch voice agents and link call recordings.
                </div>

                <!-- Sarvam Samvaad Webhook Setup helper for user -->
                <div style="background:#faf5ff; border:1px solid #d8b4fe; border-radius:10px; padding:14px; margin-bottom:16px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <span style="font-weight:700; font-size:12px; color:#6b21a8;">
                            ⚡ Sarvam Samvaad Webhook (Paste in indus.sarvam.ai/samvaad/settings/webhooks)
                        </span>
                        <span style="font-size:10.5px; background:#f3e8ff; color:#7e22ce; padding:2px 6px; border-radius:4px; font-weight:700;">
                            Live Endpoint
                        </span>
                    </div>
                    <div style="font-size:11.5px; color:#475569; margin-bottom:8px;">
                        To receive live call events, transcripts, recordings, and lead summaries, fill your Sarvam "Add Webhook" popup as follows:
                    </div>
                    <div style="background:white; border:1px solid #e9d5ff; border-radius:6px; padding:8px 10px; font-size:11.5px; font-family:monospace; margin-bottom:6px;">
                        <div style="color:#64748b; font-size:10.5px;">Name:</div>
                        <div style="font-weight:700; color:#0f172a;">Pundeer AI SalesHub Webhook</div>
                        <div style="color:#64748b; font-size:10.5px; margin-top:6px;">Callback URL:</div>
                        <div style="font-weight:700; color:#3568ca; word-break:break-all;" id="sarvam-webhook-url">{{ url('/webhook/sarvam/calls') }}</div>
                        <div style="color:#64748b; font-size:10.5px; margin-top:6px;">Headers:</div>
                        <div style="color:#334155;">(Leave blank or default)</div>
                    </div>
                    <button
                        type="button"
                        onclick="navigator.clipboard.writeText(document.getElementById('sarvam-webhook-url').innerText); alert('Sarvam Callback URL copied to clipboard!');"
                        style="background:#7e22ce; color:white; border:none; border-radius:6px; padding:4px 10px; font-size:11px; font-weight:700; cursor:pointer;"
                    >
                        📋 Copy Callback URL
                    </button>
                </div>
            @endif

            <form wire:submit="saveConnection">
                <div style="margin-bottom:12px;">
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Business Scope</label>
                    <select wire:model="business_id" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:7px 10px; font-size:12.5px; background:white;">
                        <option value="">All Businesses (Global Shared)</option>
                        @foreach($businesses as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom:12px;">
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Connection Name *</label>
                    <input wire:model="name" type="text" placeholder="e.g. Sarvam AI Voice Desk" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:7px 10px; font-size:12.5px;">
                    @error('name') <span style="color:#dc2626; font-size:11px;">{{ $message }}</span> @enderror
                </div>

                @if($selectedPreset === 'sarvam')
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:12px;">
                        <div>
                            <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Workspace ID</label>
                            <input wire:model="sarvam_workspace_id" type="text" placeholder="ws_xxxx" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:7px 10px; font-size:12.5px;">
                        </div>
                        <div>
                            <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Organization ID</label>
                            <input wire:model="sarvam_org_id" type="text" placeholder="org_xxxx" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:7px 10px; font-size:12.5px;">
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:12px;">
                        <div>
                            <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Sarvam Phone Number (E.164)</label>
                            <input wire:model="sarvam_phone_number" type="text" placeholder="+919876543210" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:7px 10px; font-size:12.5px;">
                        </div>
                        <div>
                            <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Inbound Deployment ID</label>
                            <input wire:model="sarvam_inbound_deployment_id" type="text" placeholder="dep_xxxx" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:7px 10px; font-size:12.5px;">
                        </div>
                    </div>
                @endif

                <div style="margin-bottom:12px;">
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">API Base URL</label>
                    <input wire:model="base_url" type="text" placeholder="https://api.sarvam.ai" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:7px 10px; font-size:12.5px;">
                </div>

                <div style="margin-bottom:12px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                        <label style="font-size:11.5px; font-weight:700; color:#475569;">API Key (Encrypted server-side) *</label>
                        <button
                            type="button"
                            wire:click="fetchModelsFromApi"
                            wire:loading.attr="disabled"
                            style="border:1px solid #cbd5e1; background:#f8fafc; border-radius:5px; padding:2px 8px; font-size:10.5px; font-weight:700; color:#334155; cursor:pointer;"
                        >
                            <span wire:loading.remove wire:target="fetchModelsFromApi">🔄 Auto-Fetch Models</span>
                            <span wire:loading wire:target="fetchModelsFromApi">⏳ Fetching...</span>
                        </button>
                    </div>
                    <input
                        wire:model.live.debounce.600ms="api_key"
                        type="password"
                        placeholder="{{ $selectedPreset === 'groq' ? 'gsk_... (Paste your Groq API Key)' : 'Enter API Key / Token' }}"
                        style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:7px 10px; font-size:12.5px;"
                    >
                    @error('api_key') <span style="color:#dc2626; font-size:11px;">{{ $message }}</span> @enderror

                    @if($modelsFetchStatus)
                        <div style="font-size:11px; color:#15803d; margin-top:4px; font-weight:600;">
                            {{ $modelsFetchStatus }}
                        </div>
                    @endif
                    @if($modelsFetchError)
                        <div style="font-size:11px; color:#dc2626; margin-top:4px;">
                            {{ $modelsFetchError }}
                        </div>
                    @endif
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:16px;">
                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                            <label style="font-size:11.5px; font-weight:700; color:#475569;">Model / Agent ID *</label>
                            <button
                                type="button"
                                wire:click="$toggle('isCustomModel')"
                                style="background:none; border:none; padding:0; font-size:10.5px; color:#3b82f6; text-decoration:underline; cursor:pointer;"
                            >
                                {{ $isCustomModel ? 'Select from list' : 'Custom Model' }}
                            </button>
                        </div>

                        @if($isCustomModel)
                            <input
                                wire:model="custom_model_id"
                                type="text"
                                placeholder="e.g. llama-3.3-70b-versatile"
                                style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:7px 10px; font-size:12.5px;"
                            >
                        @else
                            <select
                                wire:model="model_or_agent_id"
                                style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:7px 10px; font-size:12.5px; background:white;"
                            >
                                @if(!empty($availableModels))
                                    <optgroup label="{{ ucfirst($provider_name) }} Models">
                                        @foreach($availableModels as $id => $label)
                                            <option value="{{ $id }}">{{ is_string($label) && $label !== $id ? $label : $id }}</option>
                                        @endforeach
                                    </optgroup>
                                @else
                                    <option value="{{ $model_or_agent_id }}">{{ $model_or_agent_id }}</option>
                                @endif
                            </select>
                        @endif
                    </div>
                    <div>
                        <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Priority</label>
                        <select wire:model="priority" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:7px 10px; font-size:12.5px; background:white;">
                            <option value="primary">Primary (Default)</option>
                            <option value="fallback_1">Fallback 1</option>
                            <option value="fallback_2">Fallback 2</option>
                        </select>
                    </div>
                </div>

                <button type="submit" style="width:100%; background:#3978f6; color:white; border:0; border-radius:7px; padding:9px; font-size:13px; font-weight:700; cursor:pointer;">
                    Save Connection
                </button>
            </form>
        </div>
    </div>
</div>

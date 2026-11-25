<div style="max-width:800px; margin:0 auto;">
    <div style="margin-bottom:20px;">
        <a href="{{ route('admin.ai-agents.show', $agent) }}" style="color:#3978f6; font-size:13px; text-decoration:none; font-weight:600;">
            ← Back to Agent Details
        </a>
        <h1 style="font-size:24px; font-weight:800; margin:8px 0 4px; color:#0f172a;">Edit Agent: {{ $agent->name }}</h1>
        <p style="margin:0; color:#64748b; font-size:13.5px;">Configure role, voice telephony, or WhatsApp knowledge bases.</p>
    </div>

    <form wire:submit="update" style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:26px; box-shadow:0 8px 24px rgba(0,0,0,.04);">
        <!-- Agent Channel / Purpose Selection -->
        <div style="margin-bottom:18px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px;">
            <label style="display:block; font-size:12px; font-weight:700; color:#1e293b; margin-bottom:8px;">
                Agent Channel / Role *
            </label>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <label style="display:flex; align-items:flex-start; gap:10px; padding:12px; border-radius:8px; border:2px solid {{ $agent_type === 'whatsapp_chat' ? '#3978f6' : '#cbd5e1' }}; background:{{ $agent_type === 'whatsapp_chat' ? '#eff6ff' : 'white' }}; cursor:pointer;">
                    <input type="radio" wire:model.live="agent_type" value="whatsapp_chat" style="margin-top:2px;">
                    <div>
                        <div style="font-weight:700; font-size:13px; color:#0f172a;">💬 WhatsApp & Messaging AI</div>
                        <div style="font-size:11.5px; color:#64748b; margin-top:2px;">
                            Answers customer inquiries on WhatsApp, uses RAG knowledge docs, and qualifies leads.
                        </div>
                    </div>
                </label>

                <label style="display:flex; align-items:flex-start; gap:10px; padding:12px; border-radius:8px; border:2px solid {{ $agent_type === 'voice_calling' ? '#3978f6' : '#cbd5e1' }}; background:{{ $agent_type === 'voice_calling' ? '#eff6ff' : 'white' }}; cursor:pointer;">
                    <input type="radio" wire:model.live="agent_type" value="voice_calling" style="margin-top:2px;">
                    <div>
                        <div style="font-weight:700; font-size:13px; color:#0f172a;">🎙️ Voice Calling AI (Sarvam AI)</div>
                        <div style="font-size:11.5px; color:#64748b; margin-top:2px;">
                            Dedicated telephony voice bot powered by Sarvam AI for natural Hindi phone calls.
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                Agent Name *
            </label>
            <input
                wire:model="name"
                type="text"
                style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px;"
            >
            @error('name') <span style="color:#dc2626; font-size:11.5px;">{{ $message }}</span> @enderror
        </div>

        @if($agent_type === 'voice_calling')
            <div style="background:#faf5ff; border:1px solid #e9d5ff; border-radius:10px; padding:16px; margin-bottom:18px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <div>
                        <div style="font-size:13px; font-weight:700; color:#6b21a8;">
                            🎙️ Sarvam AI Telephony Voice Agent Settings
                        </div>
                        <div style="font-size:11.5px; color:#7e22ce;">
                            Configure the voice persona and live agent from Sarvam Samvaad.
                        </div>
                    </div>
                    <button
                        type="button"
                        wire:click="fetchSarvamVoiceAgents"
                        wire:loading.attr="disabled"
                        style="border:1px solid #d8b4fe; background:#f3e8ff; border-radius:6px; padding:4px 10px; font-size:11px; font-weight:700; color:#6b21a8; cursor:pointer;"
                    >
                        <span wire:loading.remove wire:target="fetchSarvamVoiceAgents">🔄 Auto-Fetch Agents from Sarvam</span>
                        <span wire:loading wire:target="fetchSarvamVoiceAgents">⏳ Fetching Agents...</span>
                    </button>
                </div>

                @if($sarvamFetchMessage)
                    <div style="background:#ecfdf5; border:1px solid #bbf7d0; border-radius:6px; padding:6px 10px; font-size:11.5px; color:#15803d; font-weight:600; margin-bottom:10px;">
                        {{ $sarvamFetchMessage }}
                    </div>
                @endif
                @if($sarvamFetchError)
                    <div style="background:#fff1f2; border:1px solid #fecdd3; border-radius:6px; padding:6px 10px; font-size:11.5px; color:#be123c; margin-bottom:10px;">
                        {{ $sarvamFetchError }}
                    </div>
                @endif

                <div style="display:grid; grid-template-columns:1.5fr 1fr 1fr; gap:12px;">
                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                            <label style="font-size:11.5px; font-weight:700; color:#475569;">
                                Sarvam Voice Agent *
                            </label>
                            <button
                                type="button"
                                wire:click="$toggle('isCustomSarvamAgent')"
                                style="background:none; border:none; padding:0; font-size:10.5px; color:#7e22ce; text-decoration:underline; cursor:pointer;"
                            >
                                {{ $isCustomSarvamAgent ? 'Select from list' : 'Custom Agent ID' }}
                            </button>
                        </div>

                        @if($isCustomSarvamAgent)
                            <input
                                wire:model="sarvam_agent_id"
                                type="text"
                                placeholder="e.g. agnt_sarvam_garlic_desk_01"
                                style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:8px 10px; font-size:12.5px; background:white;"
                            >
                        @else
                            <select
                                wire:model="sarvam_agent_id"
                                style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:8px 10px; font-size:12.5px; background:white;"
                            >
                                @foreach($sarvamVoiceAgents as $svAgent)
                                    <option value="{{ $svAgent['id'] }}">
                                        {{ $svAgent['name'] ?? $svAgent['id'] }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div>
                        <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">
                            Voice Language
                        </label>
                        <select wire:model="voice_language" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:8px 10px; font-size:12.5px; background:white;">
                            <option value="hi-IN">Hindi (hi-IN)</option>
                            <option value="en-IN">Indian English (en-IN)</option>
                            <option value="hinglish">Hinglish Conversational</option>
                            <option value="pa-IN">Punjabi (pa-IN)</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">
                            Voice Gender / Persona
                        </label>
                        <select wire:model="voice_gender" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:8px 10px; font-size:12.5px; background:white;">
                            <option value="female">Female (Natural)</option>
                            <option value="male">Male (Executive)</option>
                        </select>
                    </div>
                </div>
            </div>
        @endif

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                    Model Name / Identifier *
                </label>
                <input
                    wire:model="model"
                    type="text"
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px;"
                >
                @error('model') <span style="color:#dc2626; font-size:11.5px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                    Personality Tone *
                </label>
                <select
                    wire:model="personality_tone"
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px; background:white;"
                >
                    <option value="professional">Professional & Helpful</option>
                    <option value="consultative">Consultative & Solution-oriented</option>
                    <option value="commercial">Commercial / Fast Quotation</option>
                    <option value="friendly">Friendly & Warm</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                System Instructions / Prompt
            </label>
            <textarea
                wire:model="system_instructions"
                rows="4"
                style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px;"
            ></textarea>
        </div>

        @if($agent_type === 'whatsapp_chat')
            <div style="margin-bottom:20px;">
                <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                    Assigned Knowledge Bases
                </label>
                @if($eligibleKbs->isNotEmpty())
                    <div style="border:1px solid #cbd5e1; border-radius:8px; padding:12px; max-height:140px; overflow-y:auto; background:#f8fafc;">
                        @foreach($eligibleKbs as $kb)
                            <label style="display:flex; align-items:center; gap:8px; font-size:12.5px; color:#334155; margin-bottom:6px; cursor:pointer;">
                                <input wire:model="assigned_kb_ids" type="checkbox" value="{{ $kb->id }}" style="width:16px; height:16px;">
                                <b>{{ $kb->name }}</b>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <a href="{{ route('admin.ai-agents.show', $agent) }}" style="padding:9px 16px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none;">
                Cancel
            </a>
            <button
                type="submit"
                style="padding:9px 20px; border:0; background:#3978f6; color:white; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;"
            >
                Save Changes
            </button>
        </div>
    </form>
</div>

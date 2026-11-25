<div>
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:18px; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 style="font-size:24px; font-weight:800; margin:0 0 6px; color:#0f172a;">AI Agents</h1>
            <p style="margin:0; color:#64748b; font-size:13.5px;">Configure WhatsApp Chatbots and Sarvam AI Voice Calling Agents, paired per business workspace.</p>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('admin.ai-agents.create') }}" style="background:#3978f6; color:white; padding:9px 16px; border-radius:9px; font-size:13px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                ＋ Create AI Agent
            </a>
        </div>
    </div>

    <!-- Channel Filter Tabs & Business Filter -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px; background:white; padding:10px 14px; border:1px solid #e2e8f0; border-radius:12px;">
        <div style="display:flex; gap:6px; flex-wrap:wrap;">
            <button
                type="button"
                wire:click="setChannelFilter('all')"
                style="padding:6px 14px; border-radius:8px; font-size:12.5px; font-weight:700; cursor:pointer; border:1px solid {{ $channelFilter === 'all' ? '#3978f6' : '#e2e8f0' }}; background:{{ $channelFilter === 'all' ? '#eff6ff' : '#f8fafc' }}; color:{{ $channelFilter === 'all' ? '#1d4ed8' : '#475569' }};"
            >
                🌟 All Agents ({{ $totalCount }})
            </button>
            <button
                type="button"
                wire:click="setChannelFilter('whatsapp_chat')"
                style="padding:6px 14px; border-radius:8px; font-size:12.5px; font-weight:700; cursor:pointer; border:1px solid {{ $channelFilter === 'whatsapp_chat' ? '#10b981' : '#e2e8f0' }}; background:{{ $channelFilter === 'whatsapp_chat' ? '#ecfdf5' : '#f8fafc' }}; color:{{ $channelFilter === 'whatsapp_chat' ? '#047857' : '#475569' }};"
            >
                💬 WhatsApp Chat AI ({{ $chatCount }})
            </button>
            <button
                type="button"
                wire:click="setChannelFilter('voice_calling')"
                style="padding:6px 14px; border-radius:8px; font-size:12.5px; font-weight:700; cursor:pointer; border:1px solid {{ $channelFilter === 'voice_calling' ? '#8b5cf6' : '#e2e8f0' }}; background:{{ $channelFilter === 'voice_calling' ? '#faf5ff' : '#f8fafc' }}; color:{{ $channelFilter === 'voice_calling' ? '#6d28d9' : '#475569' }};"
            >
                🎙️ Sarvam Voice Calling ({{ $voiceCount }})
            </button>
        </div>

        <div style="display:flex; align-items:center; gap:8px;">
            <label style="font-size:12px; color:#64748b; font-weight:600;">Business:</label>
            <select wire:model.live="businessFilter" style="border:1px solid #cbd5e1; border-radius:7px; padding:6px 10px; font-size:12px; background:white;">
                <option value="all">All Businesses</option>
                @foreach($businesses as $b)
                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Agent Cards Grid matching prototype -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(340px, 1fr)); gap:18px; margin-bottom:22px;">
        @forelse($agents as $agent)
            @php
                $isVoice = $agent->agent_type === 'voice_calling';
                $companionVoice = $isVoice ? null : $companionAgents->first(fn($a) => $a->business_id === $agent->business_id && $a->agent_type === 'voice_calling' && $a->id !== $agent->id);
                $companionChat = !$isVoice ? null : $companionAgents->first(fn($a) => $a->business_id === $agent->business_id && $a->agent_type === 'whatsapp_chat' && $a->id !== $agent->id);
            @endphp
            <div style="background:white; border:1px solid {{ $isVoice ? '#e9d5ff' : '#e2e8f0' }}; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03); display:flex; flex-direction:column; justify-content:space-between;">
                <div>
                    <!-- Channel Badge & Status -->
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                        @if($isVoice)
                            <span style="font-size:11px; font-weight:700; padding:3px 9px; border-radius:8px; background:#faf5ff; border:1px solid #d8b4fe; color:#7e22ce; display:inline-flex; align-items:center; gap:4px;">
                                🎙️ Voice Calling AI (Sarvam Telephony)
                            </span>
                        @else
                            <span style="font-size:11px; font-weight:700; padding:3px 9px; border-radius:8px; background:#eff6ff; border:1px solid #bfdbfe; color:#1d4ed8; display:inline-flex; align-items:center; gap:4px;">
                                💬 WhatsApp Chat AI (Groq/OpenAI)
                            </span>
                        @endif

                        <span style="font-size:11px; font-weight:700; padding:2px 8px; border-radius:12px; {{ $agent->is_active ? 'background:#ecfdf5; color:#16a34a;' : 'background:#fef2f2; color:#dc2626;' }}">
                            {{ $agent->is_active ? '● Active' : '● Paused' }}
                        </span>
                    </div>

                    <div style="margin-bottom:8px;">
                        <h3 style="font-size:16px; font-weight:700; margin:0; color:#0f172a;">{{ $agent->name }}</h3>
                        <div style="font-size:12px; color:#3568ca; font-weight:600; margin-top:2px;">
                            🏢 {{ $agent->business->name }}
                        </div>
                    </div>

                    <p style="color:#64748b; font-size:12.5px; line-height:1.5; margin:0 0 14px;">
                        {{ $agent->description ?: ($isVoice ? 'Handles inbound and outbound telephony calls in Hindi/English.' : 'Handles customer enquiries on WhatsApp using business RAG.') }}
                    </p>

                    <div style="background:#f8fafc; border-radius:8px; padding:10px; font-size:11.5px; color:#475569; margin-bottom:12px; line-height:1.5;">
                        <div>Model: <b>{{ $agent->model }}</b> · Tone: <b>{{ ucfirst($agent->personality_tone) }}</b></div>
                        <div style="margin-top:3px;">
                            @if($isVoice)
                                Voice Persona: <b>{{ $agent->voice_config['voice_language'] ?? 'hi-IN' }} ({{ $agent->voice_config['voice_gender'] ?? 'female' }})</b>
                            @else
                                Knowledge Collections:
                                <b>{{ $agent->knowledgeBases->pluck('name')->implode(', ') ?: 'Default Business KB' }}</b>
                            @endif
                        </div>
                    </div>

                    <!-- Paired Companion Channel Box -->
                    <div style="background:{{ $isVoice ? '#f0fdf4' : '#faf5ff' }}; border:1px solid {{ $isVoice ? '#bbf7d0' : '#e9d5ff' }}; border-radius:8px; padding:8px 10px; font-size:11.5px; margin-bottom:14px;">
                        @if($isVoice)
                            @if($companionChat)
                                <div style="display:flex; justify-content:space-between; align-items:center;">
                                    <span style="color:#166534;">
                                        💬 <b>Paired WhatsApp Bot:</b> {{ $companionChat->name }}
                                    </span>
                                    <a href="{{ route('admin.ai-agents.edit', $companionChat) }}" style="color:#15803d; font-weight:700; text-decoration:none;">Configure →</a>
                                </div>
                            @else
                                <div style="display:flex; justify-content:space-between; align-items:center;">
                                    <span style="color:#64748b;">💬 No WhatsApp Bot paired for this business</span>
                                    <a href="{{ route('admin.ai-agents.create') }}" style="color:#15803d; font-weight:700; text-decoration:none;">+ Add WhatsApp Bot</a>
                                </div>
                            @endif
                        @else
                            @if($companionVoice)
                                <div style="display:flex; justify-content:space-between; align-items:center;">
                                    <span style="color:#6b21a8;">
                                        🎙️ <b>Paired Voice Desk:</b> {{ $companionVoice->name }}
                                    </span>
                                    <a href="{{ route('admin.ai-agents.edit', $companionVoice) }}" style="color:#7e22ce; font-weight:700; text-decoration:none;">Configure →</a>
                                </div>
                            @else
                                <div style="display:flex; justify-content:space-between; align-items:center;">
                                    <span style="color:#64748b;">🎙️ No Voice Desk paired for this business</span>
                                    <a href="{{ route('admin.ai-agents.create') }}" style="color:#7e22ce; font-weight:700; text-decoration:none;">+ Add Voice Desk</a>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #f1f5f9; padding-top:12px;">
                    <div style="display:flex; gap:6px;">
                        <a href="{{ route('admin.ai-agents.show', $agent) }}" style="padding:6px 12px; background:#edf3ff; color:#3568ca; border-radius:6px; font-size:12px; font-weight:700; text-decoration:none;">
                            {{ $isVoice ? 'Call Desk →' : 'Test Playground →' }}
                        </a>
                        <a href="{{ route('admin.ai-agents.edit', $agent) }}" style="padding:6px 10px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:6px; font-size:12px; font-weight:600; text-decoration:none;">
                            Configure
                        </a>
                    </div>
                    <button
                        wire:confirm="Delete agent {{ $agent->name }}?"
                        wire:click="deleteAgent({{ $agent->id }})"
                        style="padding:6px 8px; border:1px solid #fee2e2; background:#fef2f2; color:#dc2626; border-radius:6px; font-size:11.5px; cursor:pointer;"
                    >
                        ✕
                    </button>
                </div>
            </div>
        @empty
            <div style="grid-column:1/-1; background:white; border:1px solid #e2e8f0; border-radius:14px; padding:40px; text-align:center; color:#64748b;">
                No AI agents found for this filter.
            </div>
        @endforelse
    </div>

    <!-- Agent Behavior Control Panel matching prototype -->
    <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:22px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
        <h3 style="font-size:16px; font-weight:700; margin:0 0 14px; color:#0f172a;">Strict Agent Behavioral Safeguards</h3>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:18px;">
            <div style="border-left:3px solid #3978f6; padding-left:14px;">
                <b style="font-size:13.5px; color:#0f172a;">Unknown Answer Policy</b>
                <p style="color:#64748b; font-size:12.5px; margin:4px 0 0; line-height:1.5;">
                    AI is strictly forbidden from guessing prices, inventories, or delivery timelines. When knowledge is missing, it asks for clarification or offers a callback.
                </p>
            </div>
            <div style="border-left:3px solid #16a34a; padding-left:14px;">
                <b style="font-size:13.5px; color:#0f172a;">Human Takeover Coordination</b>
                <p style="color:#64748b; font-size:12.5px; margin:4px 0 0; line-height:1.5;">
                    The moment a human sales representative replies or clicks "Take Over", AI replies are permanently suspended until explicitly resumed.
                </p>
            </div>
            <div style="border-left:3px solid #8057e8; padding-left:14px;">
                <b style="font-size:13.5px; color:#0f172a;">Multi-Tenant RAG Isolation</b>
                <p style="color:#64748b; font-size:12.5px; margin:4px 0 0; line-height:1.5;">
                    Agents can only query vector embeddings and knowledge chunks belonging to their assigned business. Garlic documents are inaccessible to Pundeer agents.
                </p>
            </div>
        </div>
    </div>
</div>

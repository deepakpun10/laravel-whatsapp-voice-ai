<div>
    <!-- Header -->
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:18px; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 style="font-size:24px; font-weight:800; margin:0 0 4px; color:#0f172a;">WhatsApp Inbox</h1>
            <p style="margin:0; color:#64748b; font-size:13.5px;">Shared line incoming chats, routed by business with seamless human takeover.</p>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('admin.integrations.index') }}" style="background:white; border:1px solid #cbd5e1; color:#334155; padding:8px 14px; border-radius:8px; font-size:12.5px; font-weight:600; text-decoration:none;">
                WhatsApp API Settings
            </a>
        </div>
    </div>

    <!-- 3-Column Inbox Grid matching prototype -->
    <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; box-shadow:0 8px 28px rgba(27,43,76,.07); display:grid; grid-template-columns:290px minmax(0, 1fr) 270px; min-height:600px; overflow:hidden;">
        <!-- Left: Conversations List -->
        <div style="border-right:1px solid #e8ecf3; display:flex; flex-direction:column; background:#ffffff;">
            <div style="padding:14px 16px; border-bottom:1px solid #e8ecf3; display:flex; justify-content:space-between; align-items:center;">
                <span style="font-weight:700; font-size:14px; color:#0f172a;">Conversations</span>
                <span style="background:#edf3ff; color:#3568ca; font-size:11px; font-weight:700; padding:2px 8px; border-radius:12px;">
                    {{ $conversations->count() }} active
                </span>
            </div>

            <div style="padding:10px 14px; border-bottom:1px solid #f1f5f9;">
                <input
                    wire:model.live.debounce.250ms="search"
                    type="text"
                    placeholder="Search chats..."
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:7px 10px; font-size:12.5px;"
                >
            </div>

            <div style="flex:1; overflow-y:auto; max-height:550px;">
                @forelse($conversations as $conv)
                    @php $isActive = $selectedConversationId === $conv->id; @endphp
                    <div
                        wire:click="selectConversation({{ $conv->id }})"
                        style="padding:13px 14px; border-bottom:1px solid #f1f5f9; cursor:pointer; background:{{ $isActive ? '#f2f6ff' : 'white' }}; border-left:{{ $isActive ? '3px solid #3978f6' : '3px solid transparent' }}; transition:all .15s;"
                    >
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px; gap:6px;">
                            <div style="min-width:0; display:flex; align-items:center; gap:5px; overflow:hidden;">
                                <b style="font-size:13px; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $conv->contact?->name ?? 'WhatsApp Lead' }}</b>
                                @if($conv->contact?->phone)
                                    <span style="font-size:11px; color:#64748b; font-family:monospace; white-space:nowrap;">({{ $conv->contact->phone }})</span>
                                @endif
                            </div>
                            @if($conv->business)
                                <span style="font-size:10px; font-weight:700; padding:2px 6px; border-radius:4px; {{ $conv->business->code === 'GAR-002' ? 'background:#e7f8f8; color:#16818b;' : 'background:#edf3ff; color:#3568ca;' }}">
                                    {{ $conv->business->name }}
                                </span>
                            @else
                                <span style="background:#fffbeb; color:#b45309; font-size:10px; font-weight:700; padding:2px 6px; border-radius:4px;">
                                    Review
                                </span>
                            @endif
                        </div>
                        <p style="margin:0; color:#64748b; font-size:11.5px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $conv->messages->last()?->content ?? 'No messages yet' }}
                        </p>
                    </div>
                @empty
                    <div style="padding:30px 16px; text-align:center; color:#94a3b8; font-size:12.5px;">
                        No conversations found. Inbound messages from WhatsApp will automatically populate here.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Middle: Chat Area -->
        <div style="display:flex; flex-direction:column; background:#ffffff; min-width:0;">
            @if($selectedConversation)
                <!-- Chat Header -->
                <div style="padding:12px 20px; border-bottom:1px solid #e8ecf3; display:flex; justify-content:space-between; align-items:center; background:#fff;">
                    <div>
                        <b style="font-size:14px; color:#0f172a;">{{ $selectedConversation->contact?->name ?? 'WhatsApp Lead' }}</b>
                        <div style="font-size:11.5px; color:#64748b;">
                            {{ $selectedConversation->contact?->phone ?: 'No phone' }} · Channel: {{ ucfirst($selectedConversation->channel) }}
                        </div>
                    </div>
                    <div style="display:flex; gap:8px;">
                        <button
                            wire:click="toggleTakeover"
                            style="padding:6px 12px; border-radius:7px; font-size:12px; font-weight:700; cursor:pointer; {{ $selectedConversation->isAiActive() ? 'background:#edf3ff; color:#3568ca; border:1px solid #dce8ff;' : 'background:#fff7ed; color:#c2410c; border:1px solid #ffedd5;' }}"
                        >
                            {{ $selectedConversation->isAiActive() ? 'Take Over' : 'Resume AI' }}
                        </button>
                    </div>
                </div>

                <!-- Chat Messages Body -->
                <div style="flex:1; padding:18px; background:#f8faff; overflow-y:auto; display:flex; flex-direction:column; gap:10px; max-height:450px;">
                    @forelse($selectedConversation->messages as $msg)
                        @php $isMe = in_array($msg->sender_type, ['ai', 'human']); @endphp
                        <div style="max-width:75%; padding:10px 14px; border-radius:12px; font-size:12.5px; line-height:1.5; {{ $isMe ? 'margin-left:auto; background:#eaf2ff; border:1px solid #dce8ff; color:#1e293b;' : 'background:white; border:1px solid #e8ecf3; color:#1e293b;' }}">
                            @if($msg->message_type === 'interactive' && !empty($msg->raw_payload['interactive']))
                                @php $inter = $msg->raw_payload['interactive']; @endphp
                                @if(!empty($inter['header']['text']))
                                    <div style="font-weight:700; font-size:12px; margin-bottom:4px; color:#0f172a;">{{ $inter['header']['text'] }}</div>
                                @endif
                                <div>{{ $msg->content }}</div>

                                @if(!empty($inter['action']['buttons']))
                                    <div style="display:flex; flex-direction:column; gap:4px; margin-top:8px;">
                                        @foreach($inter['action']['buttons'] as $b)
                                            <div style="background:white; border:1px solid #3b82f6; color:#2563eb; font-weight:700; font-size:11.5px; padding:6px 10px; border-radius:6px; text-align:center;">
                                                🔘 {{ $b['reply']['title'] ?? 'Option' }}
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif(!empty($inter['action']['sections']))
                                    <div style="margin-top:8px; border-top:1px dashed #cbd5e1; padding-top:6px;">
                                        <span style="background:#e0e7ff; color:#3730a3; font-size:11px; font-weight:700; padding:2px 8px; border-radius:4px;">
                                            📋 List Message ({{ count($inter['action']['sections'][0]['rows'] ?? []) }} options)
                                        </span>
                                    </div>
                                @endif

                                @if(!empty($inter['footer']['text']))
                                    <div style="font-size:10.5px; color:#94a3b8; margin-top:4px;">{{ $inter['footer']['text'] }}</div>
                                @endif
                            @elseif($msg->message_type === 'interactive' && ! $isMe)
                                <div style="display:inline-flex; align-items:center; gap:6px; background:#f0fdf4; border:1px solid #86efac; color:#15803d; font-weight:700; padding:4px 10px; border-radius:8px; font-size:12px;">
                                    <span>🔘 Selected:</span>
                                    <span>{{ $msg->content }}</span>
                                </div>
                            @else
                                <div>{{ $msg->content }}</div>
                            @endif

                            <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; margin-top:4px; font-size:10px; color:#64748b;">
                                <span>
                                    {{ $msg->sender_type === 'ai' ? '✧ Business AI' : ($msg->sender_type === 'human' ? '👤 Support Agent' : 'Customer') }}
                                    @if($msg->message_type === 'interactive')
                                        <span style="background:#dbeafe; color:#1e40af; padding:1px 5px; border-radius:4px; font-weight:600; margin-left:4px;">Interactive</span>
                                    @endif
                                </span>
                                <span>{{ $msg->created_at->format('h:i A') }}</span>
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center; padding:40px; color:#94a3b8; font-size:13px;">
                            No messages in this conversation thread yet.
                        </div>
                    @endforelse
                </div>

                <!-- Interactive Template Quick Dispatcher -->
                @if(isset($availableTemplates) && $availableTemplates->isNotEmpty())
                    <div style="padding:8px 16px; background:#f8fafc; border-top:1px solid #e2e8f0; display:flex; align-items:center; gap:8px;">
                        <span style="font-size:11px; font-weight:700; color:#475569; white-space:nowrap;">⚡ Send Interactive:</span>
                        <select wire:model="selectedInteractiveTemplateId" style="flex:1; border:1px solid #cbd5e1; border-radius:6px; padding:6px 8px; font-size:12px; background:white;">
                            <option value="">-- Choose Interactive Template --</option>
                            @foreach($availableTemplates as $t)
                                <option value="{{ $t->id }}">
                                    {{ $t->name }} ({{ strtoupper($t->interactive_type) }})
                                </option>
                            @endforeach
                        </select>
                        <button
                            type="button"
                            wire:click="sendInteractiveTemplate"
                            style="background:#2563eb; color:white; border:0; border-radius:6px; padding:6px 12px; font-size:11.5px; font-weight:700; cursor:pointer;"
                        >
                            Send Template
                        </button>
                    </div>
                @endif

                <!-- Chat Input Form -->
                <form wire:submit="sendMessage" style="padding:12px 16px; border-top:1px solid #e8ecf3; display:flex; gap:8px; background:#fff;">
                    <input
                        wire:model="messageText"
                        type="text"
                        placeholder="{{ $selectedConversation->isAiActive() ? 'Send human reply (AI will pause automatically on reply)...' : 'Type message to send via WhatsApp...' }}"
                        style="flex:1; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px;"
                    >
                    <button
                        type="submit"
                        style="background:#3978f6; color:white; border:0; border-radius:8px; padding:9px 18px; font-size:13px; font-weight:700; cursor:pointer;"
                    >
                        Send
                    </button>
                </form>
            @else
                <div style="display:flex; align-items:center; justify-content:center; height:100%; color:#94a3b8; font-size:14px;">
                    Select a conversation from the left to view messages.
                </div>
            @endif
        </div>

        <!-- Right: Conversation Details Sidebar -->
        <div style="border-left:1px solid #e8ecf3; padding:18px; background:#fafcff;">
            @if($selectedConversation)
                <div style="font-weight:700; font-size:13.5px; color:#0f172a; margin-bottom:14px; border-bottom:1px solid #e8ecf3; padding-bottom:10px;">
                    Conversation Details
                </div>

                <div style="background:#f1f5f9; border-radius:9px; padding:10px 12px; margin-bottom:14px;">
                    <div style="font-size:10.5px; color:#64748b; font-weight:600;">ROUTED BUSINESS</div>
                    <div style="font-weight:700; font-size:13px; color:#0f172a; margin-top:2px;">
                        {{ $selectedConversation->business->name ?? '⚠️ Unassigned Review' }}
                    </div>
                </div>

                <div style="margin-bottom:14px;">
                    <div style="font-size:11px; color:#64748b; font-weight:700;">AI AUTOMATION MODE</div>
                    <div style="margin-top:4px;">
                        @if($selectedConversation->isAiActive())
                            <span style="background:#ecfdf5; color:#16a34a; font-weight:700; font-size:11.5px; padding:3px 9px; border-radius:12px;">
                                ● AI Active
                            </span>
                        @else
                            <span style="background:#fff7ed; color:#c2410c; font-weight:700; font-size:11.5px; padding:3px 9px; border-radius:12px;">
                                ● Human Takeover
                            </span>
                        @endif
                    </div>
                </div>

                <div style="margin-bottom:14px;">
                    <div style="font-size:11px; color:#64748b; font-weight:700;">KNOWLEDGE SCOPE</div>
                    <div style="font-size:12px; color:#334155; margin-top:3px;">
                        @if($selectedConversation->business)
                            {{ $selectedConversation->business->knowledgeBases->pluck('name')->implode(', ') ?: 'Default Business KB' }}
                        @else
                            <span style="color:#94a3b8; font-style:italic;">None (Unassigned)</span>
                        @endif
                    </div>
                </div>

                <div style="margin-bottom:16px;">
                    <div style="font-size:11px; color:#64748b; font-weight:700;">LEAD STATUS</div>
                    <div style="margin-top:3px;">
                        <span style="background:#edf3ff; color:#3568ca; font-weight:700; font-size:11px; padding:2px 8px; border-radius:6px; text-transform:capitalize;">
                            {{ $selectedConversation->contact->lead_status ?? 'New' }}
                        </span>
                    </div>
                </div>

                @if($selectedConversation->summary)
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:9px; padding:10px 12px; margin-bottom:14px;">
                        <div style="font-size:10.5px; color:#475569; font-weight:700;">🧠 PERSISTENT AI SUMMARY</div>
                        <div style="font-size:11.5px; color:#334155; margin-top:3px; line-height:1.4;">
                            {{ $selectedConversation->summary }}
                        </div>
                    </div>
                @endif

                @if(!empty($selectedConversation->extracted_facts))
                    <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:9px; padding:10px 12px; margin-bottom:14px;">
                        <div style="font-size:10.5px; color:#166534; font-weight:700; margin-bottom:6px;">📊 EXTRACTED FACTS</div>
                        @foreach($selectedConversation->extracted_facts as $key => $val)
                            @if(!empty($val))
                                <div style="font-size:11px; color:#14532d; margin-bottom:3px; display:flex; justify-content:space-between;">
                                    <span style="text-transform:capitalize; font-weight:600;">{{ str_replace('_', ' ', $key) }}:</span>
                                    <span>{{ $val }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                @if($selectedConversation->contact)
                    <a href="{{ route('admin.contacts.show', $selectedConversation->contact) }}" style="display:block; text-align:center; padding:7px 12px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:7px; font-size:12px; font-weight:600; text-decoration:none;">
                        View Full Contact Profile →
                    </a>
                @endif
            @else
                <div style="color:#94a3b8; font-size:12px; text-align:center; padding-top:40px;">
                    No conversation selected.
                </div>
            @endif
        </div>
    </div>
</div>

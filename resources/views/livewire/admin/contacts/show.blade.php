<div>
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
        <div>
            <a href="{{ route('admin.contacts.index') }}" style="color:#3978f6; font-size:13px; text-decoration:none; font-weight:600;">
                ← Back to Contacts
            </a>
            <h1 style="font-size:24px; font-weight:800; margin:6px 0 4px; color:#0f172a;">{{ $contact->name }}</h1>
            <p style="margin:0; color:#64748b; font-size:13.5px;">
                Phone: <b>{{ $contact->phone }}</b> · Source: {{ ucfirst($contact->source) }}
            </p>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('admin.conversations.index', ['contact_id' => $contact->id]) }}" style="padding:8px 14px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none;">
                💬 Open WhatsApp Chat
            </a>
            <a href="{{ route('admin.contacts.edit', $contact) }}" style="padding:8px 16px; background:#3978f6; color:white; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none;">
                Edit Contact
            </a>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:2fr 1fr; gap:20px;">
        <!-- Left Column: Details & Conversations History -->
        <div style="display:flex; flex-direction:column; gap:20px;">
            <!-- Lead Profile Card -->
            <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
                <h3 style="font-size:15px; font-weight:700; margin:0 0 14px; color:#0f172a;">Lead Profile</h3>
                <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:14px; font-size:13px;">
                    <div>
                        <div style="color:#64748b; font-size:11.5px;">Assigned Business</div>
                        <div style="font-weight:700; color:#0f172a; margin-top:2px;">
                            {{ $contact->business->name ?? '⚠️ Unassigned Review' }}
                        </div>
                    </div>
                    <div>
                        <div style="color:#64748b; font-size:11.5px;">Lead Status</div>
                        <div style="margin-top:2px;">
                            <span style="background:#edf3ff; color:#3568ca; padding:2px 8px; border-radius:12px; font-weight:700; font-size:11.5px; text-transform:capitalize;">
                                {{ str_replace('_', ' ', $contact->lead_status) }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <div style="color:#64748b; font-size:11.5px;">Requirement</div>
                        <div style="font-weight:600; color:#0f172a; margin-top:2px;">{{ $contact->requirement ?: 'Not specified' }}</div>
                    </div>
                    <div>
                        <div style="color:#64748b; font-size:11.5px;">Budget / Volume</div>
                        <div style="font-weight:600; color:#0f172a; margin-top:2px;">{{ $contact->budget ?: 'Not specified' }}</div>
                    </div>
                    <div>
                        <div style="color:#64748b; font-size:11.5px;">Location</div>
                        <div style="font-weight:600; color:#0f172a; margin-top:2px;">{{ $contact->location ?: 'Not specified' }}</div>
                    </div>
                    <div>
                        <div style="color:#64748b; font-size:11.5px;">Email</div>
                        <div style="font-weight:600; color:#0f172a; margin-top:2px;">{{ $contact->email ?: 'N/A' }}</div>
                    </div>
                </div>

                @if($contact->notes)
                    <div style="margin-top:14px; border-top:1px solid #f1f5f9; padding-top:12px;">
                        <div style="color:#64748b; font-size:11.5px; margin-bottom:4px;">Notes:</div>
                        <p style="color:#334155; font-size:12.5px; margin:0; line-height:1.5;">{{ $contact->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Conversations History -->
            <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
                <h3 style="font-size:15px; font-weight:700; margin:0 0 14px; color:#0f172a;">Conversations History</h3>
                @forelse($contact->conversations as $conv)
                    <div style="border:1px solid #f1f5f9; border-radius:10px; padding:14px; margin-bottom:12px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                            <div style="font-weight:700; font-size:13px; color:#0f172a;">
                                {{ $conv->business->name ?? 'Unassigned' }} · Channel: {{ ucfirst($conv->channel) }}
                            </div>
                            <span style="background:{{ $conv->isAiActive() ? '#ecfdf5' : '#fffbeb' }}; color:{{ $conv->isAiActive() ? '#16a34a' : '#b45309' }}; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:700;">
                                {{ $conv->isAiActive() ? 'AI Active' : 'Human Takeover' }}
                            </span>
                        </div>
                        <div style="font-size:12px; color:#64748b;">
                            {{ $conv->messages->count() }} message(s) recorded in thread.
                        </div>
                    </div>
                @empty
                    <p style="color:#94a3b8; font-size:13px; margin:0;">No message history recorded yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Quick Actions & Business Assignment -->
        <div style="display:flex; flex-direction:column; gap:20px;">
            <!-- Reassign Business Widget -->
            <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
                <h3 style="font-size:15px; font-weight:700; margin:0 0 10px; color:#0f172a;">Business Assignment</h3>
                <p style="font-size:12px; color:#64748b; margin:0 0 12px;">
                    Lock or reassign which business's AI agent, pricing, and RAG documents apply to this contact.
                </p>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    @foreach($businesses as $b)
                        <button
                            wire:click="assignBusiness({{ $b->id }})"
                            style="text-align:left; padding:8px 12px; border-radius:8px; border:1px solid {{ $contact->business_id === $b->id ? '#3978f6' : '#e2e8f0' }}; background:{{ $contact->business_id === $b->id ? '#edf3ff' : 'white' }}; color:{{ $contact->business_id === $b->id ? '#3568ca' : '#334155' }}; font-weight:{{ $contact->business_id === $b->id ? '700' : '500' }}; font-size:12.5px; cursor:pointer;"
                        >
                            {{ $b->name }}
                        </button>
                    @endforeach
                    <button
                        wire:click="assignBusiness(null)"
                        style="text-align:left; padding:8px 12px; border-radius:8px; border:1px solid #fed7aa; background:#fffbeb; color:#b45309; font-size:12.5px; cursor:pointer;"
                    >
                        Mark Unassigned
                    </button>
                </div>
            </div>

            <!-- Quick Lead Status Update -->
            <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
                <h3 style="font-size:15px; font-weight:700; margin:0 0 10px; color:#0f172a;">Update Lead Status</h3>
                <div style="display:flex; flex-wrap:wrap; gap:6px;">
                    @foreach(['new', 'interested', 'qualified', 'follow_up', 'converted', 'lost'] as $st)
                        <button
                            wire:click="updateStatus('{{ $st }}')"
                            style="padding:5px 10px; border-radius:6px; border:1px solid {{ $contact->lead_status === $st ? '#3978f6' : '#cbd5e1' }}; background:{{ $contact->lead_status === $st ? '#3978f6' : 'white' }}; color:{{ $contact->lead_status === $st ? 'white' : '#334155' }}; font-size:11.5px; font-weight:600; cursor:pointer; text-transform:capitalize;"
                        >
                            {{ str_replace('_', ' ', $st) }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

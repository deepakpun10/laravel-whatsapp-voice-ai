<div>
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
        <div>
            <a href="{{ route('admin.businesses.index') }}" style="color:#3978f6; font-size:13px; text-decoration:none; font-weight:600;">
                ← Back to Businesses
            </a>
            <h1 style="font-size:24px; font-weight:800; margin:6px 0 4px; color:#0f172a;">{{ $business->name }}</h1>
            <p style="margin:0; color:#64748b; font-size:13.5px;">
                Code: <code style="background:#f1f5f9; padding:2px 6px; border-radius:4px; font-weight:700;">{{ $business->code ?? 'N/A' }}</code> · Category: {{ $business->category ?? 'General' }}
            </p>
        </div>
        <div style="display:flex; gap:10px;">
            <button
                wire:click="toggleStatus"
                style="padding:8px 14px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;"
            >
                {{ $business->status === 'active' ? 'Deactivate' : 'Activate' }}
            </button>
            <a href="{{ route('admin.businesses.edit', $business) }}" style="padding:8px 16px; background:#3978f6; color:white; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none;">
                Edit Details
            </a>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:2fr 1fr; gap:20px;">
        <!-- Left: Details & Scoped Resources -->
        <div style="display:flex; flex-direction:column; gap:20px;">
            <!-- Overview Card -->
            <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
                <h3 style="font-size:15px; font-weight:700; margin:0 0 12px; color:#0f172a;">Business Overview</h3>
                <p style="color:#475569; font-size:13px; line-height:1.6; margin:0 0 14px;">
                    {{ $business->description ?: 'No description provided.' }}
                </p>
                <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:12px; border-top:1px solid #f1f5f9; padding-top:14px; font-size:12.5px;">
                    <div>
                        <div style="color:#64748b;">Timezone</div>
                        <div style="font-weight:700; color:#0f172a; margin-top:2px;">{{ $business->timezone }}</div>
                    </div>
                    <div>
                        <div style="color:#64748b;">Default Language</div>
                        <div style="font-weight:700; color:#0f172a; margin-top:2px;">{{ $business->default_language }}</div>
                    </div>
                    <div>
                        <div style="color:#64748b;">Status</div>
                        <div style="margin-top:2px;">
                            <span style="background:{{ $business->status === 'active' ? '#ecfdf5' : '#fef2f2' }}; color:{{ $business->status === 'active' ? '#16a34a' : '#dc2626' }}; padding:2px 8px; border-radius:20px; font-weight:700; font-size:11px;">
                                {{ ucfirst($business->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Routing Rules Scoped to this Business -->
            <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <h3 style="font-size:15px; font-weight:700; margin:0; color:#0f172a;">
                        Configured Routing Rules ({{ $business->routingRules->count() }})
                    </h3>
                    <a href="{{ route('admin.routing.create', ['business_id' => $business->id]) }}" style="font-size:12px; color:#3978f6; font-weight:700; text-decoration:none;">
                        ＋ Add Rule
                    </a>
                </div>

                @forelse($business->routingRules as $rule)
                    <div style="border:1px solid #f1f5f9; border-radius:8px; padding:12px; margin-bottom:8px; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <div style="font-weight:700; font-size:13px; color:#0f172a;">{{ $rule->name }}</div>
                            <div style="margin-top:4px; display:flex; gap:4px; flex-wrap:wrap;">
                                @foreach($rule->keywords as $kw)
                                    <span style="background:#edf3ff; color:#3568ca; padding:2px 6px; border-radius:4px; font-size:11px; font-weight:600;">
                                        {{ $kw }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <div style="text-align:right; font-size:11.5px; color:#64748b;">
                            <div>Priority: <b>{{ ucfirst($rule->priority) }}</b></div>
                            <div>Min Conf: <b>{{ $rule->min_confidence }}</b></div>
                        </div>
                    </div>
                @empty
                    <p style="color:#94a3b8; font-size:12.5px; margin:0;">No routing rules configured for this business yet.</p>
                @endforelse
            </div>

            <!-- Assigned AI Agents -->
            <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <h3 style="font-size:15px; font-weight:700; margin:0; color:#0f172a;">
                        Dedicated AI Agents ({{ $business->aiAgents->count() }})
                    </h3>
                    <a href="{{ route('admin.ai-agents.create', ['business_id' => $business->id]) }}" style="font-size:12px; color:#3978f6; font-weight:700; text-decoration:none;">
                        ＋ Create Agent
                    </a>
                </div>

                @forelse($business->aiAgents as $agent)
                    <div style="border:1px solid #f1f5f9; border-radius:8px; padding:12px; margin-bottom:8px; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <div style="font-weight:700; font-size:13px; color:#0f172a;">{{ $agent->name }}</div>
                            <div style="color:#64748b; font-size:11.5px;">Model: {{ $agent->model }} · Tone: {{ $agent->personality_tone }}</div>
                        </div>
                        <a href="{{ route('admin.ai-agents.show', $agent) }}" style="font-size:12px; color:#3978f6; font-weight:600; text-decoration:none;">
                            Configure →
                        </a>
                    </div>
                @empty
                    <p style="color:#94a3b8; font-size:12.5px; margin:0;">No AI agents assigned to this business yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Right: Team & Knowledge Bases -->
        <div style="display:flex; flex-direction:column; gap:20px;">
            <!-- Knowledge Base Scopes -->
            <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <h3 style="font-size:15px; font-weight:700; margin:0; color:#0f172a;">
                        Knowledge Bases ({{ $business->knowledgeBases->count() }})
                    </h3>
                    <a href="{{ route('admin.knowledge-bases.create', ['business_id' => $business->id]) }}" style="font-size:12px; color:#3978f6; font-weight:700; text-decoration:none;">
                        ＋ New KB
                    </a>
                </div>

                @forelse($business->knowledgeBases as $kb)
                    <div style="border:1px solid #f1f5f9; border-radius:8px; padding:10px; margin-bottom:8px;">
                        <div style="font-weight:700; font-size:12.5px; color:#0f172a;">{{ $kb->name }}</div>
                        <div style="color:#64748b; font-size:11px;">Status: {{ $kb->status }}</div>
                    </div>
                @empty
                    <p style="color:#94a3b8; font-size:12px; margin:0;">No Knowledge Bases created yet.</p>
                @endforelse
            </div>

            <!-- Team Members -->
            <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
                <h3 style="font-size:15px; font-weight:700; margin:0 0 12px; color:#0f172a;">Assigned Team Members</h3>
                @forelse($business->users as $user)
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f8fafc;">
                        <div>
                            <div style="font-weight:600; font-size:12.5px; color:#0f172a;">{{ $user->name }}</div>
                            <div style="color:#64748b; font-size:11px;">{{ $user->email }}</div>
                        </div>
                        <span style="background:#f1f5f9; color:#475569; padding:2px 7px; border-radius:4px; font-size:10.5px; font-weight:700;">
                            {{ ucfirst(str_replace('_', ' ', $user->pivot->role ?? 'agent')) }}
                        </span>
                    </div>
                @empty
                    <p style="color:#94a3b8; font-size:12px; margin:0;">No team members assigned.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

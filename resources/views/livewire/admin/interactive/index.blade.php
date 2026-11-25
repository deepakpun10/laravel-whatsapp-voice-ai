<div>
    <!-- Top Header -->
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 style="font-size:24px; font-weight:800; margin:0 0 6px; color:#0f172a;">Interactive Messages & Builder</h1>
            <p style="margin:0; color:#64748b; font-size:13.5px;">Manage WhatsApp reply buttons, list messages, automation rules, and AI decision triggers.</p>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('admin.interactive.create') }}" style="background:#3978f6; color:white; padding:9px 18px; border-radius:8px; font-weight:700; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                <span>+</span> New Interactive Template
            </a>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div style="display:flex; gap:8px; margin-bottom:22px; border-bottom:1px solid #e2e8f0; padding-bottom:8px; overflow-x:auto;">
        <button
            type="button"
            wire:click="switchTab('templates')"
            style="padding:8px 16px; border-radius:8px; font-size:13px; font-weight:700; border:none; cursor:pointer; background:{{ $activeTab === 'templates' ? '#3978f6' : 'transparent' }}; color:{{ $activeTab === 'templates' ? 'white' : '#64748b' }};"
        >
            🗂️ Interactive Templates
        </button>
        <button
            type="button"
            wire:click="switchTab('rules')"
            style="padding:8px 16px; border-radius:8px; font-size:13px; font-weight:700; border:none; cursor:pointer; background:{{ $activeTab === 'rules' ? '#3978f6' : 'transparent' }}; color:{{ $activeTab === 'rules' ? 'white' : '#64748b' }};"
        >
            ⚙️ Automation Rules
        </button>
        <button
            type="button"
            wire:click="switchTab('settings')"
            style="padding:8px 16px; border-radius:8px; font-size:13px; font-weight:700; border:none; cursor:pointer; background:{{ $activeTab === 'settings' ? '#3978f6' : 'transparent' }}; color:{{ $activeTab === 'settings' ? 'white' : '#64748b' }};"
        >
            🤖 AI Interactive Settings
        </button>
        <button
            type="button"
            wire:click="switchTab('analytics')"
            style="padding:8px 16px; border-radius:8px; font-size:13px; font-weight:700; border:none; cursor:pointer; background:{{ $activeTab === 'analytics' ? '#3978f6' : 'transparent' }}; color:{{ $activeTab === 'analytics' ? 'white' : '#64748b' }};"
        >
            📊 Usage Analytics
        </button>
    </div>

    <!-- TAB 1: TEMPLATES -->
    @if($activeTab === 'templates')
        <!-- Filters Bar -->
        <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:14px; margin-bottom:20px; display:flex; gap:12px; flex-wrap:wrap; align-items:center; justify-content:space-between;">
            <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search templates or body text..."
                    style="border:1px solid #cbd5e1; border-radius:8px; padding:7px 12px; font-size:12.5px; width:240px;"
                >

                <select wire:model.live="businessFilter" style="border:1px solid #cbd5e1; border-radius:8px; padding:7px 12px; font-size:12.5px; background:white;">
                    <option value="all">All Businesses</option>
                    @foreach($businesses as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="typeFilter" style="border:1px solid #cbd5e1; border-radius:8px; padding:7px 12px; font-size:12.5px; background:white;">
                    <option value="all">All Types</option>
                    <option value="button">Reply Buttons (Up to 3)</option>
                    <option value="list">List Messages (Menu)</option>
                    <option value="cta_url">Call to Action (URL)</option>
                </select>
            </div>
            <div style="font-size:12px; color:#64748b;">
                Showing <b>{{ $templates->total() }}</b> interactive templates
            </div>
        </div>

        <!-- Templates Table -->
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; box-shadow:0 6px 20px rgba(0,0,0,.03);">
            <table style="width:100%; border-collapse:collapse; text-align:left; font-size:13px;">
                <thead>
                    <tr style="border-bottom:1px solid #e2e8f0; background:#f8fafc; color:#64748b; font-size:11.5px;">
                        <th style="padding:12px 16px;">TEMPLATE NAME</th>
                        <th style="padding:12px 16px;">BUSINESS</th>
                        <th style="padding:12px 16px;">TYPE</th>
                        <th style="padding:12px 16px;">OPTIONS / BUTTONS</th>
                        <th style="padding:12px 16px;">AI TRIGGER</th>
                        <th style="padding:12px 16px;">STATUS</th>
                        <th style="padding:12px 16px; text-align:right;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $t)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:14px 16px;">
                                <a href="{{ route('admin.interactive.show', $t) }}" style="font-weight:700; color:#0f172a; text-decoration:none;">
                                    {{ $t->name }}
                                </a>
                                <div style="font-size:11px; color:#64748b; margin-top:2px;">
                                    {{ Str::limit($t->body_text, 45) }}
                                </div>
                            </td>
                            <td style="padding:14px 16px;">
                                <span style="font-size:11.5px; font-weight:600; color:#334155;">
                                    {{ $t->business->name }}
                                </span>
                            </td>
                            <td style="padding:14px 16px;">
                                @if($t->interactive_type === 'button')
                                    <span style="background:#eff6ff; color:#2563eb; font-size:11px; font-weight:700; padding:3px 8px; border-radius:8px;">
                                        🔘 Buttons ({{ $t->buttons->count() }})
                                    </span>
                                @elseif($t->interactive_type === 'list')
                                    <span style="background:#f5f3ff; color:#7c3aed; font-size:11px; font-weight:700; padding:3px 8px; border-radius:8px;">
                                        ☰ List Message
                                    </span>
                                @else
                                    <span style="background:#fff7ed; color:#c2410c; font-size:11px; font-weight:700; padding:3px 8px; border-radius:8px;">
                                        🔗 CTA Link
                                    </span>
                                @endif
                            </td>
                            <td style="padding:14px 16px;">
                                @if($t->interactive_type === 'button')
                                    <div style="display:flex; gap:4px; flex-wrap:wrap;">
                                        @foreach($t->buttons as $b)
                                            <span style="background:#f1f5f9; color:#475569; font-size:10.5px; padding:2px 6px; border-radius:4px; border:1px solid #e2e8f0;">
                                                {{ $b->title }}
                                            </span>
                                        @endforeach
                                    </div>
                                @elseif($t->interactive_type === 'list')
                                    <span style="font-size:11.5px; color:#64748b;">
                                        {{ $t->sections->sum(fn($s) => $s->rows->count()) }} selectable rows
                                    </span>
                                @else
                                    <span style="font-size:11.5px; color:#64748b;">{{ $t->cta_display_text }}</span>
                                @endif
                            </td>
                            <td style="padding:14px 16px;">
                                <span style="font-size:11px; font-weight:600; padding:2px 8px; border-radius:10px; {{ $t->ai_availability ? 'background:#ecfdf5; color:#16a34a;' : 'background:#f1f5f9; color:#64748b;' }}">
                                    {{ $t->ai_availability ? 'AI Enabled' : 'Manual Only' }}
                                </span>
                            </td>
                            <td style="padding:14px 16px;">
                                <button
                                    type="button"
                                    wire:click="toggleStatus({{ $t->id }})"
                                    style="padding:3px 8px; border-radius:10px; font-size:11px; font-weight:700; border:none; cursor:pointer; {{ $t->status === 'active' ? 'background:#ecfdf5; color:#16a34a;' : 'background:#fee2e2; color:#dc2626;' }}"
                                >
                                    ● {{ ucfirst($t->status) }}
                                </button>
                            </td>
                            <td style="padding:14px 16px; text-align:right;">
                                <a href="{{ route('admin.interactive.show', $t) }}" style="padding:4px 8px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:5px; font-size:11px; font-weight:600; text-decoration:none; margin-right:4px;">
                                    View
                                </a>
                                <a href="{{ route('admin.interactive.edit', $t) }}" style="padding:4px 8px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:5px; font-size:11px; font-weight:600; text-decoration:none; margin-right:4px;">
                                    Edit
                                </a>
                                <button
                                    type="button"
                                    wire:click="duplicateTemplate({{ $t->id }})"
                                    style="padding:4px 8px; border:1px solid #cbd5e1; background:#f8fafc; color:#334155; border-radius:5px; font-size:11px; cursor:pointer; margin-right:4px;"
                                >
                                    Copy
                                </button>
                                <button
                                    type="button"
                                    wire:confirm="Delete template {{ $t->name }}?"
                                    wire:click="deleteTemplate({{ $t->id }})"
                                    style="padding:4px 6px; border:1px solid #fee2e2; background:#fef2f2; color:#dc2626; border-radius:5px; font-size:11px; cursor:pointer;"
                                >
                                    ✕
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:36px; color:#94a3b8;">
                                No interactive templates created yet. Click "+ New Interactive Template" to get started!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div style="padding:14px 16px; border-top:1px solid #f1f5f9;">
                {{ $templates->links() }}
            </div>
        </div>
    @endif

    <!-- TAB 2: AUTOMATION RULES -->
    @if($activeTab === 'rules')
        <div style="display:grid; grid-template-columns:1.8fr 1.2fr; gap:20px;">
            <!-- Left: Rules List -->
            <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
                <h3 style="font-size:16px; font-weight:700; margin:0 0 14px; color:#0f172a;">Configured Automation Rules</h3>
                <div style="space-y:12px;">
                    @forelse($rules as $r)
                        <div style="border:1px solid #e2e8f0; border-radius:10px; padding:14px; margin-bottom:12px; background:#f8fafc; display:flex; justify-content:space-between; align-items:flex-start;">
                            <div>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <b style="font-size:13.5px; color:#0f172a;">{{ $r->name }}</b>
                                    <span style="font-size:10.5px; font-weight:700; padding:2px 7px; border-radius:10px; background:#e0e7ff; color:#4338ca;">
                                        Pri: {{ $r->priority }}
                                    </span>
                                    <span style="font-size:10.5px; font-weight:700; padding:2px 7px; border-radius:10px; {{ $r->is_active ? 'background:#ecfdf5; color:#16a34a;' : 'background:#fee2e2; color:#dc2626;' }}">
                                        {{ $r->is_active ? 'Active' : 'Disabled' }}
                                    </span>
                                </div>
                                <div style="font-size:12px; color:#475569; margin-top:4px;">
                                    Business: <b>{{ $r->business->name }}</b> | Trigger: <code>{{ $r->trigger_type }}</code>
                                </div>
                                <div style="font-size:11.5px; color:#64748b; margin-top:3px;">
                                    Sends Template: <b>{{ $r->template->name }}</b> ({{ $r->template->interactive_type }})
                                </div>
                                @if(!empty($r->trigger_keywords))
                                    <div style="display:flex; gap:4px; margin-top:6px; flex-wrap:wrap;">
                                        @foreach($r->trigger_keywords as $kw)
                                            <span style="background:white; border:1px solid #cbd5e1; font-size:10px; padding:1px 5px; border-radius:4px; color:#334155;">
                                                #{{ $kw }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div style="display:flex; gap:6px;">
                                <button
                                    type="button"
                                    wire:click="toggleRuleStatus({{ $r->id }})"
                                    style="padding:4px 8px; border:1px solid #cbd5e1; background:white; font-size:11px; border-radius:5px; cursor:pointer;"
                                >
                                    {{ $r->is_active ? 'Pause' : 'Activate' }}
                                </button>
                                <button
                                    type="button"
                                    wire:click="deleteRule({{ $r->id }})"
                                    style="padding:4px 6px; border:1px solid #fee2e2; background:#fef2f2; color:#dc2626; font-size:11px; border-radius:5px; cursor:pointer;"
                                >
                                    ✕
                                </button>
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center; padding:32px; color:#94a3b8; font-size:13px;">
                            No automation rules configured yet. Create one on the right!
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right: Add Automation Rule Form -->
            <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
                <h3 style="font-size:16px; font-weight:700; margin:0 0 14px; color:#0f172a;">New Automation Rule</h3>
                <form wire:submit="saveRule">
                    <div style="margin-bottom:12px;">
                        <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Rule Name *</label>
                        <input wire:model="rule_name" type="text" placeholder="e.g. Website Inquiry -> Service Buttons" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:7px 10px; font-size:12.5px;">
                        @error('rule_name') <span style="color:#dc2626; font-size:11px;">{{ $message }}</span> @enderror
                    </div>

                    <div style="margin-bottom:12px;">
                        <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Business *</label>
                        <select wire:model.live="rule_business_id" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:7px 10px; font-size:12.5px; background:white;">
                            @foreach($businesses as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom:12px;">
                        <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Interactive Template to Send *</label>
                        <select wire:model="rule_template_id" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:7px 10px; font-size:12.5px; background:white;">
                            <option value="">-- Choose Template --</option>
                            @foreach(\App\Models\InteractiveMessageTemplate::where('business_id', $rule_business_id)->where('status', 'active')->get() as $tmpl)
                                <option value="{{ $tmpl->id }}">{{ $tmpl->name }} ({{ $tmpl->interactive_type }})</option>
                            @endforeach
                        </select>
                        @error('rule_template_id') <span style="color:#dc2626; font-size:11px;">{{ $message }}</span> @enderror
                    </div>

                    <div style="margin-bottom:12px;">
                        <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Trigger Condition</label>
                        <select wire:model="rule_trigger_type" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:7px 10px; font-size:12.5px; background:white;">
                            <option value="intent_detected">When Customer Intent Detected</option>
                            <option value="keywords">When Message Contains Keywords</option>
                            <option value="first_inquiry">On First Incoming Message (Welcome)</option>
                            <option value="budget_stage">When Customer Mentions Budget</option>
                        </select>
                    </div>

                    <div style="margin-bottom:12px;">
                        <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Trigger Keywords (comma separated)</label>
                        <input wire:model="rule_keywords" type="text" placeholder="e.g. website, web design, store" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:7px 10px; font-size:12.5px;">
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:14px;">
                        <div>
                            <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Priority (1=High)</label>
                            <input wire:model="rule_priority" type="number" min="1" max="100" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:7px 10px; font-size:12.5px;">
                        </div>
                        <div>
                            <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Send Mode</label>
                            <select wire:model="rule_send_mode" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:7px 10px; font-size:12.5px; background:white;">
                                <option value="automatic">Automatic</option>
                                <option value="ai_assisted">AI Confirmed</option>
                                <option value="first_inquiry_only">First Message Only</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" style="width:100%; background:#3978f6; color:white; border:0; border-radius:7px; padding:9px; font-size:13px; font-weight:700; cursor:pointer;">
                        Save Automation Rule
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- TAB 3: AI INTERACTIVE SETTINGS -->
    @if($activeTab === 'settings')
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:20px;">
            @forelse($agentSettings as $setting)
                <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                        <div>
                            <h3 style="font-size:16px; font-weight:700; margin:0; color:#0f172a;">{{ $setting->aiAgent->name }}</h3>
                            <span style="font-size:11.5px; color:#64748b;">Business: {{ $setting->aiAgent->business->name }}</span>
                        </div>
                        <span style="font-size:11px; font-weight:700; padding:3px 8px; border-radius:10px; background:#ecfdf5; color:#16a34a;">
                            {{ strtoupper(str_replace('_', ' ', $setting->mode)) }}
                        </span>
                    </div>

                    <div style="margin-bottom:14px; background:#f8fafc; border-radius:8px; padding:12px; font-size:12px; color:#475569; line-height:1.5;">
                        Configure whether this AI Agent selects buttons automatically from approved templates, follows strict rules, or requires human intervention.
                    </div>

                    <div style="margin-bottom:14px;">
                        <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:6px;">Interactive Decision Mode</label>
                        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:6px;">
                            <button
                                type="button"
                                wire:click="updateAgentMode({{ $setting->id }}, 'manual')"
                                style="padding:6px; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer; border:1px solid {{ $setting->mode === 'manual' ? '#ef4444' : '#cbd5e1' }}; background:{{ $setting->mode === 'manual' ? '#fef2f2' : 'white' }}; color:{{ $setting->mode === 'manual' ? '#b91c1c' : '#475569' }};"
                            >
                                1. Manual Only
                            </button>
                            <button
                                type="button"
                                wire:click="updateAgentMode({{ $setting->id }}, 'rule_based')"
                                style="padding:6px; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer; border:1px solid {{ $setting->mode === 'rule_based' ? '#f59e0b' : '#cbd5e1' }}; background:{{ $setting->mode === 'rule_based' ? '#fffbeb' : 'white' }}; color:{{ $setting->mode === 'rule_based' ? '#b45309' : '#475569' }};"
                            >
                                2. Rule Based
                            </button>
                            <button
                                type="button"
                                wire:click="updateAgentMode({{ $setting->id }}, 'ai_assisted')"
                                style="padding:6px; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer; border:1px solid {{ $setting->mode === 'ai_assisted' ? '#10b981' : '#cbd5e1' }}; background:{{ $setting->mode === 'ai_assisted' ? '#ecfdf5' : 'white' }}; color:{{ $setting->mode === 'ai_assisted' ? '#047857' : '#475569' }};"
                            >
                                3. AI Assisted
                            </button>
                        </div>
                    </div>

                    <div style="border-top:1px solid #f1f5f9; padding-top:10px; font-size:11.5px; color:#64748b; display:flex; justify-content:space-between;">
                        <span>Max Buttons / Msg: <b>{{ $setting->max_buttons_per_message }}</b></span>
                        <span>List Allowed: <b>{{ $setting->allow_list_messages ? 'Yes' : 'No' }}</b></span>
                        <span>Cooldown: <b>{{ $setting->cooldown_seconds }}s</b></span>
                    </div>
                </div>
            @empty
                <div style="text-align:center; padding:30px; color:#94a3b8;">No AI agents registered yet.</div>
            @endforelse
        </div>
    @endif

    <!-- TAB 4: USAGE ANALYTICS -->
    @if($activeTab === 'analytics')
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-bottom:24px;">
            <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:18px;">
                <div style="font-size:11.5px; color:#64748b; font-weight:700;">TOTAL INTERACTIVE SENT</div>
                <div style="font-size:28px; font-weight:800; color:#0f172a; margin-top:4px;">{{ $totalSent }}</div>
            </div>
            <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:18px;">
                <div style="font-size:11.5px; color:#64748b; font-weight:700;">BUTTON / LIST CLICKS</div>
                <div style="font-size:28px; font-weight:800; color:#16a34a; margin-top:4px;">{{ $totalClicked }}</div>
            </div>
            <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:18px;">
                <div style="font-size:11.5px; color:#64748b; font-weight:700;">INTERACTION RATE (CTR)</div>
                <div style="font-size:28px; font-weight:800; color:#3978f6; margin-top:4px;">{{ $ctr }}%</div>
            </div>
        </div>

        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
            <h3 style="font-size:16px; font-weight:700; margin:0 0 14px; color:#0f172a;">Top Selected Button & List Choices</h3>
            <table style="width:100%; border-collapse:collapse; font-size:13px; text-align:left;">
                <thead>
                    <tr style="border-bottom:1px solid #e2e8f0; background:#f8fafc; color:#64748b; font-size:11.5px;">
                        <th style="padding:10px 14px;">OPTION / BUTTON TITLE</th>
                        <th style="padding:10px 14px; text-align:right;">TIMES CLICKED</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topSelections as $sel)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:12px 14px; font-weight:600; color:#0f172a;">
                                🔘 {{ $sel->selected_title }}
                            </td>
                            <td style="padding:12px 14px; text-align:right; font-weight:700; color:#16a34a;">
                                {{ $sel->count }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="text-align:center; padding:24px; color:#94a3b8;">
                                No interactive click events recorded yet. Click events are tracked live when customers tap buttons on WhatsApp!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>

<div style="padding: 24px 28px; max-width: 1300px; margin: 0 auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <!-- Top Header -->
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 style="font-size:24px; font-weight:800; margin:0 0 6px; color:#0f172a;">⚡ Calling Workflows & Automation</h1>
            <p style="margin:0; color:#64748b; font-size:13.5px;">Trigger Sarvam AI voice calls, callbacks, and CRM actions based on customer events and lead score qualification.</p>
        </div>
        <div>
            <button
                type="button"
                wire:click="openCreateModal"
                style="background:#3978f6; color:white; border:none; padding:9px 18px; border-radius:8px; font-weight:700; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:6px;"
            >
                <span>+</span> New Calling Workflow
            </button>
        </div>
    </div>

    <!-- Filters Bar -->
    <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:14px; margin-bottom:20px; display:flex; gap:12px; flex-wrap:wrap; align-items:center; justify-content:space-between;">
        <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search workflow name..."
                style="border:1px solid #cbd5e1; border-radius:8px; padding:7px 12px; font-size:12.5px; width:220px;"
            >
            <select wire:model.live="businessFilter" style="border:1px solid #cbd5e1; border-radius:8px; padding:7px 12px; font-size:12.5px; background:white;">
                <option value="all">All Businesses</option>
                @foreach($businesses as $b)
                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="triggerFilter" style="border:1px solid #cbd5e1; border-radius:8px; padding:7px 12px; font-size:12.5px; background:white;">
                <option value="all">All Triggers</option>
                <option value="callback_requested">Customer Callback Request</option>
                <option value="call_me_button">Call Me WhatsApp Button</option>
                <option value="new_enquiry">New WhatsApp Enquiry</option>
                <option value="new_lead">New CRM Lead</option>
                <option value="score_threshold">High Lead Score</option>
            </select>
        </div>
        <div style="font-size:12px; color:#64748b;">
            Active Workflows: <b>{{ $workflows->where('is_active', true)->count() }}</b> / {{ $workflows->count() }}
        </div>
    </div>

    <!-- Workflows Table -->
    <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; box-shadow:0 6px 20px rgba(0,0,0,.03); margin-bottom:24px;">
        <table style="width:100%; border-collapse:collapse; text-align:left; font-size:13px;">
            <thead>
                <tr style="border-bottom:1px solid #e2e8f0; background:#f8fafc; color:#64748b; font-size:11.5px;">
                    <th style="padding:12px 16px;">WORKFLOW NAME</th>
                    <th style="padding:12px 16px;">BUSINESS</th>
                    <th style="padding:12px 16px;">TRIGGER EVENT</th>
                    <th style="padding:12px 16px;">CONDITIONS</th>
                    <th style="padding:12px 16px;">ACTION</th>
                    <th style="padding:12px 16px;">PRIORITY / RUNS</th>
                    <th style="padding:12px 16px;">STATUS</th>
                    <th style="padding:12px 16px; text-align:right;">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workflows as $wf)
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:14px 16px;">
                            <div style="font-weight:700; color:#0f172a; font-size:13.5px;">{{ $wf->name }}</div>
                            <div style="color:#64748b; font-size:11.5px;">Agent: {{ $wf->aiAgent->name ?? 'Default Business Voice Agent' }}</div>
                        </td>
                        <td style="padding:14px 16px;">
                            <span style="font-weight:600; color:#0f172a;">{{ $wf->business->name ?? 'Shared' }}</span>
                        </td>
                        <td style="padding:14px 16px;">
                            <span style="background:#eff6ff; color:#1e40af; font-weight:700; font-size:11px; padding:3px 8px; border-radius:6px; text-transform:uppercase;">
                                ⚡ {{ str_replace('_', ' ', $wf->trigger_type) }}
                            </span>
                        </td>
                        <td style="padding:14px 16px; font-size:11.5px; color:#475569;">
                            <div>Score ≥ {{ $wf->conditions['min_score'] ?? 0 }}</div>
                            <div>Consent: {{ ($wf->conditions['require_consent'] ?? true) ? 'Required' : 'Any' }}</div>
                        </td>
                        <td style="padding:14px 16px;">
                            <span style="background:#f0fdf4; color:#15803d; font-weight:700; font-size:11px; padding:3px 8px; border-radius:6px;">
                                📞 {{ str_replace('_', ' ', $wf->actions['primary_action'] ?? 'initiate_call') }}
                            </span>
                        </td>
                        <td style="padding:14px 16px; font-size:12px; color:#64748b;">
                            <div>Priority: <b>{{ $wf->priority }}</b></div>
                            <div>Runs: <b>{{ $wf->execution_count }}</b></div>
                        </td>
                        <td style="padding:14px 16px;">
                            <span style="font-size:11px; font-weight:700; padding:3px 9px; border-radius:12px; {{ $wf->is_active ? 'background:#dcfce7; color:#15803d;' : 'background:#f1f5f9; color:#64748b;' }}">
                                ● {{ $wf->is_active ? 'Active' : 'Paused' }}
                            </span>
                        </td>
                        <td style="padding:14px 16px; text-align:right;">
                            <div style="display:inline-flex; gap:6px;">
                                <button
                                    type="button"
                                    wire:click="toggleActive({{ $wf->id }})"
                                    style="background:#f8fafc; border:1px solid #cbd5e1; color:#334155; font-size:11px; font-weight:600; padding:4px 8px; border-radius:6px; cursor:pointer;"
                                >
                                    {{ $wf->is_active ? 'Pause' : 'Activate' }}
                                </button>
                                <button
                                    type="button"
                                    wire:click="duplicateWorkflow({{ $wf->id }})"
                                    style="background:#f8fafc; border:1px solid #cbd5e1; color:#334155; font-size:11px; font-weight:600; padding:4px 8px; border-radius:6px; cursor:pointer;"
                                >
                                    Copy
                                </button>
                                <button
                                    type="button"
                                    wire:click="editWorkflow({{ $wf->id }})"
                                    style="background:#f8fafc; border:1px solid #cbd5e1; color:#334155; font-size:11px; font-weight:600; padding:4px 8px; border-radius:6px; cursor:pointer;"
                                >
                                    Edit
                                </button>
                                <button
                                    type="button"
                                    wire:click="deleteWorkflow({{ $wf->id }})"
                                    style="background:#f8fafc; border:1px solid #cbd5e1; color:#dc2626; font-size:11px; font-weight:600; padding:4px 8px; border-radius:6px; cursor:pointer;"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:36px; color:#94a3b8;">
                            No calling workflows configured yet. Click "+ New Calling Workflow" to automate AI callbacks!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- MODAL: CREATE / EDIT WORKFLOW -->
    @if($showModal)
        <div style="position:fixed; inset:0; background:rgba(15,23,42,0.5); backdrop-filter:blur(3px); display:flex; align-items:center; justify-content:center; z-index:9999; padding:20px;">
            <div style="background:white; border-radius:14px; width:100%; max-width:620px; box-shadow:0 20px 40px rgba(0,0,0,.2); overflow:hidden;">
                <div style="padding:18px 22px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
                    <div style="font-weight:800; font-size:16px; color:#0f172a;">
                        {{ $editingId ? 'Edit Calling Workflow' : 'Build Calling Workflow' }}
                    </div>
                    <button type="button" wire:click="$set('showModal', false)" style="background:none; border:none; font-size:18px; cursor:pointer; color:#64748b;">✕</button>
                </div>
                <div style="padding:22px; display:flex; flex-direction:column; gap:14px; max-height:500px; overflow-y:auto;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">Workflow Name *</label>
                        <input
                            wire:model="form_name"
                            type="text"
                            placeholder="e.g. Website Enquiry High-Intent Auto Callback"
                            style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px; box-sizing:border-box;"
                        >
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">Business Assignment *</label>
                            <select wire:model="form_business_id" style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px; background:white; box-sizing:border-box;">
                                @foreach($businesses as $b)
                                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">Assigned Voice Agent</label>
                            <select wire:model="form_agent_id" style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px; background:white; box-sizing:border-box;">
                                <option value="">Default Business Agent</option>
                                @foreach($agents as $ag)
                                    <option value="{{ $ag->id }}">{{ $ag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">Trigger Event *</label>
                        <select wire:model="form_trigger_type" style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px; background:white; box-sizing:border-box;">
                            <option value="callback_requested">Customer requests a callback in conversation</option>
                            <option value="call_me_button">Customer clicks 'Call Me' WhatsApp interactive button</option>
                            <option value="new_enquiry">New WhatsApp enquiry received</option>
                            <option value="new_lead">New CRM lead registered</option>
                            <option value="score_threshold">Lead score exceeds threshold</option>
                            <option value="no_response">Customer stops responding for 24+ hours</option>
                        </select>
                    </div>

                    <!-- Conditions Section -->
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px;">
                        <div style="font-weight:700; font-size:12px; color:#475569; margin-bottom:10px; text-transform:uppercase;">Qualification Conditions (AND)</div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:10px;">
                            <div>
                                <label style="display:block; font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px;">Min Lead Score</label>
                                <input wire:model="cond_min_score" type="number" style="width:100%; border:1px solid #cbd5e1; border-radius:6px; padding:6px 10px; font-size:12.5px; box-sizing:border-box;">
                            </div>
                            <div>
                                <label style="display:block; font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px;">Min Budget (₹)</label>
                                <input wire:model="cond_min_budget" type="number" style="width:100%; border:1px solid #cbd5e1; border-radius:6px; padding:6px 10px; font-size:12.5px; box-sizing:border-box;">
                            </div>
                        </div>
                        <label style="display:flex; align-items:center; gap:6px; font-size:11.5px; font-weight:600; color:#475569; cursor:pointer;">
                            <input wire:model="cond_require_consent" type="checkbox">
                            Require customer calling consent before initiating automated call
                        </label>
                    </div>

                    <!-- Action Section -->
                    <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:14px;">
                        <div style="font-weight:700; font-size:12px; color:#166534; margin-bottom:10px; text-transform:uppercase;">Workflow Action</div>
                        <div style="display:grid; grid-template-columns:1.5fr 1fr; gap:12px;">
                            <div>
                                <label style="display:block; font-size:11px; font-weight:600; color:#14532d; margin-bottom:3px;">Primary Action</label>
                                <select wire:model="action_primary" style="width:100%; border:1px solid #86efac; border-radius:6px; padding:6px 10px; font-size:12.5px; background:white; box-sizing:border-box;">
                                    <option value="initiate_call">Initiate Sarvam AI Outbound Call</option>
                                    <option value="schedule_call">Schedule Callback during business hours</option>
                                    <option value="assign_human">Assign Human Representative & Alert</option>
                                    <option value="send_whatsapp">Send WhatsApp Interactive Follow-up</option>
                                </select>
                            </div>
                            <div>
                                <label style="display:block; font-size:11px; font-weight:600; color:#14532d; margin-bottom:3px;">Delay (Minutes)</label>
                                <input wire:model="action_delay_minutes" type="number" min="0" placeholder="0 = immediate" style="width:100%; border:1px solid #86efac; border-radius:6px; padding:6px 10px; font-size:12.5px; box-sizing:border-box;">
                            </div>
                        </div>
                    </div>
                </div>
                <div style="padding:14px 22px; border-top:1px solid #e2e8f0; background:#f8fafc; display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" wire:click="$set('showModal', false)" style="padding:8px 16px; border:1px solid #cbd5e1; background:white; border-radius:8px; font-size:12.5px; font-weight:600; cursor:pointer;">
                        Cancel
                    </button>
                    <button type="button" wire:click="saveWorkflow" style="padding:8px 18px; background:#3978f6; color:white; border:none; border-radius:8px; font-size:12.5px; font-weight:700; cursor:pointer;">
                        Save Workflow
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<div style="padding: 24px 28px; max-width: 1300px; margin: 0 auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <!-- Top Header -->
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 style="font-size:24px; font-weight:800; margin:0 0 6px; color:#0f172a;">📞 Phone Numbers Management</h1>
            <p style="margin:0; color:#64748b; font-size:13.5px;">Manage Sarvam AI provisioned phone numbers, inbound/outbound routing, and voice agent assignments.</p>
        </div>
        <div style="display:flex; gap:10px;">
            <button
                type="button"
                wire:click="syncFromSarvam"
                style="background:#f1f5f9; color:#334155; border:1px solid #cbd5e1; padding:9px 16px; border-radius:8px; font-weight:700; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:6px;"
            >
                <span>🔄</span> Sync from Sarvam API
            </button>
            <button
                type="button"
                wire:click="openCreateModal"
                style="background:#3978f6; color:white; border:none; padding:9px 18px; border-radius:8px; font-weight:700; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:6px;"
            >
                <span>+</span> Add / Register Phone Number
            </button>
        </div>
    </div>

    @if($routingTestResult)
        <div style="margin-bottom:18px; padding:12px 16px; border-radius:8px; font-size:13px; font-weight:600; {{ str_contains($routingTestResult, '✓') ? 'background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0;' : 'background:#fffbeb; color:#92400e; border:1px solid #fde68a;' }}">
            {{ $routingTestResult }}
        </div>
    @endif

    <!-- Filters Bar -->
    <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:14px; margin-bottom:20px; display:flex; gap:12px; flex-wrap:wrap; align-items:center; justify-content:space-between;">
        <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search phone number or label..."
                style="border:1px solid #cbd5e1; border-radius:8px; padding:7px 12px; font-size:12.5px; width:250px;"
            >
            <select wire:model.live="businessFilter" style="border:1px solid #cbd5e1; border-radius:8px; padding:7px 12px; font-size:12.5px; background:white;">
                <option value="all">All Businesses</option>
                @foreach($businesses as $b)
                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="statusFilter" style="border:1px solid #cbd5e1; border-radius:8px; padding:7px 12px; font-size:12.5px; background:white;">
                <option value="all">All Statuses</option>
                <option value="active">Active Only</option>
                <option value="archived">Archived</option>
            </select>
        </div>
        <div style="font-size:12px; color:#64748b;">
            Total Numbers: <b>{{ $numbers->count() }}</b>
        </div>
    </div>

    <!-- Numbers Table -->
    <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; box-shadow:0 6px 20px rgba(0,0,0,.03);">
        <table style="width:100%; border-collapse:collapse; text-align:left; font-size:13px;">
            <thead>
                <tr style="border-bottom:1px solid #e2e8f0; background:#f8fafc; color:#64748b; font-size:11.5px;">
                    <th style="padding:12px 16px;">PHONE NUMBER</th>
                    <th style="padding:12px 16px;">ASSIGNED BUSINESS</th>
                    <th style="padding:12px 16px;">INBOUND AI AGENT</th>
                    <th style="padding:12px 16px;">CHANNELS</th>
                    <th style="padding:12px 16px;">DEPLOYMENT / CONNECTION ID</th>
                    <th style="padding:12px 16px;">STATUS</th>
                    <th style="padding:12px 16px; text-align:right;">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($numbers as $num)
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:14px 16px;">
                            <div style="font-weight:700; color:#0f172a; font-size:14px; display:flex; align-items:center; gap:6px;">
                                <span>{{ $num->phone_number }}</span>
                                <span style="font-size:10px; background:#f1f5f9; color:#475569; padding:2px 6px; border-radius:4px; text-transform:uppercase;">{{ $num->provider }}</span>
                            </div>
                            <div style="color:#64748b; font-size:11.5px; margin-top:2px;">{{ $num->label ?? 'Sarvam Line' }}</div>
                        </td>
                        <td style="padding:14px 16px;">
                            @if($num->business)
                                <span style="font-weight:600; color:#0f172a;">{{ $num->business->name }}</span>
                            @else
                                <span style="color:#f59e0b; font-size:12px; font-weight:600;">⚠️ Unassigned</span>
                            @endif
                        </td>
                        <td style="padding:14px 16px;">
                            @if($num->aiAgent)
                                <span style="background:#eff6ff; color:#1d4ed8; font-weight:600; font-size:11.5px; padding:3px 8px; border-radius:6px;">
                                    🤖 {{ $num->aiAgent->name }}
                                </span>
                            @else
                                <span style="color:#94a3b8; font-style:italic; font-size:12px;">No Agent Linked</span>
                            @endif
                        </td>
                        <td style="padding:14px 16px;">
                            <div style="display:flex; gap:6px;">
                                <button
                                    type="button"
                                    wire:click="toggleInbound({{ $num->id }})"
                                    style="border:none; cursor:pointer; font-size:11px; font-weight:700; padding:3px 8px; border-radius:6px; {{ $num->inbound_enabled ? 'background:#ecfdf5; color:#065f46;' : 'background:#f1f5f9; color:#94a3b8;' }}"
                                    title="Click to toggle inbound calling"
                                >
                                    📥 Inbound: {{ $num->inbound_enabled ? 'ON' : 'OFF' }}
                                </button>
                                <button
                                    type="button"
                                    wire:click="toggleOutbound({{ $num->id }})"
                                    style="border:none; cursor:pointer; font-size:11px; font-weight:700; padding:3px 8px; border-radius:6px; {{ $num->outbound_enabled ? 'background:#ecfdf5; color:#065f46;' : 'background:#f1f5f9; color:#94a3b8;' }}"
                                    title="Click to toggle outbound calling"
                                >
                                    📤 Outbound: {{ $num->outbound_enabled ? 'ON' : 'OFF' }}
                                </button>
                            </div>
                        </td>
                        <td style="padding:14px 16px; font-family:monospace; font-size:11.5px; color:#475569;">
                            <div>Dep: {{ $num->inbound_deployment_id ?: '—' }}</div>
                            <div style="color:#94a3b8;">Conn: {{ $num->telephony_connection_id ?: '—' }}</div>
                        </td>
                        <td style="padding:14px 16px;">
                            <span style="font-size:11px; font-weight:700; padding:3px 9px; border-radius:12px; {{ $num->status === 'active' ? 'background:#dcfce7; color:#15803d;' : 'background:#f1f5f9; color:#64748b;' }}">
                                ● {{ ucfirst($num->status) }}
                            </span>
                        </td>
                        <td style="padding:14px 16px; text-align:right;">
                            <div style="display:inline-flex; gap:6px;">
                                <button
                                    type="button"
                                    wire:click="testRouting({{ $num->id }})"
                                    style="background:#f8fafc; border:1px solid #cbd5e1; color:#334155; font-size:11.5px; font-weight:600; padding:4px 8px; border-radius:6px; cursor:pointer;"
                                >
                                    Test Route
                                </button>
                                <button
                                    type="button"
                                    wire:click="editNumber({{ $num->id }})"
                                    style="background:#f8fafc; border:1px solid #cbd5e1; color:#334155; font-size:11.5px; font-weight:600; padding:4px 8px; border-radius:6px; cursor:pointer;"
                                >
                                    Edit
                                </button>
                                <button
                                    type="button"
                                    wire:click="toggleStatus({{ $num->id }})"
                                    style="background:#f8fafc; border:1px solid #cbd5e1; color:#64748b; font-size:11.5px; font-weight:600; padding:4px 8px; border-radius:6px; cursor:pointer;"
                                >
                                    {{ $num->status === 'active' ? 'Archive' : 'Activate' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:36px; color:#94a3b8;">
                            No phone numbers registered yet. Click "Add / Register Phone Number" to link your Sarvam number!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- MODAL: ADD / EDIT PHONE NUMBER -->
    @if($showModal)
        <div style="position:fixed; inset:0; background:rgba(15,23,42,0.5); backdrop-filter:blur(3px); display:flex; align-items:center; justify-content:center; z-index:9999; padding:20px;">
            <div style="background:white; border-radius:14px; width:100%; max-width:540px; box-shadow:0 20px 40px rgba(0,0,0,.2); overflow:hidden;">
                <div style="padding:18px 22px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
                    <div style="font-weight:800; font-size:16px; color:#0f172a;">
                        {{ $editingId ? 'Edit Phone Number' : 'Register Sarvam Phone Number' }}
                    </div>
                    <button type="button" wire:click="$set('showModal', false)" style="background:none; border:none; font-size:18px; cursor:pointer; color:#64748b;">✕</button>
                </div>
                <div style="padding:22px; display:flex; flex-direction:column; gap:14px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">Phone Number (E.164) *</label>
                        <input
                            wire:model="form_phone_number"
                            type="text"
                            placeholder="+919876543210"
                            style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px; box-sizing:border-box;"
                        >
                        <span style="font-size:11px; color:#64748b;">Enter the exact phone number provided in your Sarvam account.</span>
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">Friendly Label</label>
                        <input
                            wire:model="form_label"
                            type="text"
                            placeholder="e.g. Pundeer Inbound Sales Helpline"
                            style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px; box-sizing:border-box;"
                        >
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">Assign Business</label>
                            <select wire:model="form_business_id" style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px; background:white; box-sizing:border-box;">
                                <option value="">-- Unassigned --</option>
                                @foreach($businesses as $b)
                                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">Inbound AI Agent</label>
                            <select wire:model="form_agent_id" style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px; background:white; box-sizing:border-box;">
                                <option value="">-- None --</option>
                                @foreach($agents as $ag)
                                    <option value="{{ $ag->id }}">{{ $ag->name }} ({{ $ag->business->name ?? 'Shared' }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">Inbound Deployment ID</label>
                            <input
                                wire:model="form_deployment_id"
                                type="text"
                                placeholder="dep_xxxx"
                                style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:12.5px; box-sizing:border-box;"
                            >
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">Connection ID</label>
                            <input
                                wire:model="form_connection_id"
                                type="text"
                                placeholder="conn_xxxx"
                                style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:12.5px; box-sizing:border-box;"
                            >
                        </div>
                    </div>

                    <div style="display:flex; gap:18px; padding:6px 0;">
                        <label style="display:flex; align-items:center; gap:8px; font-size:12.5px; font-weight:600; color:#334155; cursor:pointer;">
                            <input wire:model="form_inbound_enabled" type="checkbox">
                            Enable Inbound Calling
                        </label>
                        <label style="display:flex; align-items:center; gap:8px; font-size:12.5px; font-weight:600; color:#334155; cursor:pointer;">
                            <input wire:model="form_outbound_enabled" type="checkbox">
                            Enable Outbound Calling
                        </label>
                    </div>
                </div>
                <div style="padding:14px 22px; border-top:1px solid #e2e8f0; background:#f8fafc; display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" wire:click="$set('showModal', false)" style="padding:8px 16px; border:1px solid #cbd5e1; background:white; border-radius:8px; font-size:12.5px; font-weight:600; cursor:pointer;">
                        Cancel
                    </button>
                    <button type="button" wire:click="saveNumber" style="padding:8px 18px; background:#3978f6; color:white; border:none; border-radius:8px; font-size:12.5px; font-weight:700; cursor:pointer;">
                        Save Configuration
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

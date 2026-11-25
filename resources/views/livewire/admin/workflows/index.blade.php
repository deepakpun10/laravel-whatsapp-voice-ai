<div>
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:22px; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 style="font-size:24px; font-weight:800; margin:0 0 6px; color:#0f172a;">Workflow Builder</h1>
            <p style="margin:0; color:#64748b; font-size:13.5px;">Automate qualification, routing checks, callbacks, and notifications with duplicate prevention.</p>
        </div>
    </div>

    <!-- Active Multi-Business Architecture Visual Pipeline matching prototype -->
    <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:22px; box-shadow:0 6px 20px rgba(0,0,0,.03); margin-bottom:22px;">
        <h3 style="font-size:15px; font-weight:700; margin:0 0 16px; color:#0f172a;">Multi-Business Inbound Message Execution Pipeline</h3>
        <div style="display:flex; flex-direction:column; align-items:center; gap:8px;">
            <div style="border:1px solid #dfe6f1; border-radius:10px; background:#fff; padding:12px 18px; width:min(360px, 100%); text-align:center; box-shadow:0 3px 12px rgba(23,43,77,.05);">
                <span style="font-size:10px; font-weight:700; color:#3568ca; text-transform:uppercase;">TRIGGER</span>
                <b style="display:block; margin:2px 0; font-size:13.5px; color:#0f172a;">Incoming WhatsApp Message</b>
                <small style="color:#64748b;">Shared Inbound Number</small>
            </div>
            <div style="color:#a2afc4; font-size:16px;">↓</div>
            <div style="border:1px solid #dfe6f1; border-radius:10px; background:#fff; padding:12px 18px; width:min(360px, 100%); text-align:center; box-shadow:0 3px 12px rgba(23,43,77,.05);">
                <span style="font-size:10px; font-weight:700; color:#3568ca; text-transform:uppercase;">STEP 1: ROUTING ENGINE</span>
                <b style="display:block; margin:2px 0; font-size:13.5px; color:#0f172a;">Identify Contact & Business Intent</b>
                <small style="color:#64748b;">Known contact memory → Intent rules → Safe fallback</small>
            </div>
            <div style="color:#a2afc4; font-size:16px;">↓</div>
            <div style="border:1px solid #dfe6f1; border-radius:10px; background:#fff; padding:12px 18px; width:min(360px, 100%); text-align:center; box-shadow:0 3px 12px rgba(23,43,77,.05);">
                <span style="font-size:10px; font-weight:700; color:#3568ca; text-transform:uppercase;">STEP 2: RAG ISOLATION</span>
                <b style="display:block; margin:2px 0; font-size:13.5px; color:#0f172a;">Load Scoped Business Documents</b>
                <small style="color:#64748b;">Never retrieve competing business prices</small>
            </div>
            <div style="color:#a2afc4; font-size:16px;">↓</div>
            <div style="border:1px solid #dfe6f1; border-radius:10px; background:#fff; padding:12px 18px; width:min(360px, 100%); text-align:center; box-shadow:0 3px 12px rgba(23,43,77,.05);">
                <span style="font-size:10px; font-weight:700; color:#3568ca; text-transform:uppercase;">ACTION</span>
                <b style="display:block; margin:2px 0; font-size:13.5px; color:#0f172a;">Send WhatsApp / Handover / Callback</b>
                <small style="color:#64748b;">Automated reply delivery with human pause guard</small>
            </div>
        </div>
    </div>

    <!-- Workflows Grid and Create Form -->
    <div style="display:grid; grid-template-columns:1.5fr 1fr; gap:20px;">
        <!-- Left: Configured Workflows -->
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
            <h3 style="font-size:16px; font-weight:700; margin:0 0 14px; color:#0f172a;">Configured Workflows</h3>
            @forelse($workflows as $wf)
                <div style="border:1px solid #f1f5f9; border-radius:10px; padding:14px; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <div style="font-weight:700; font-size:13.5px; color:#0f172a;">{{ $wf->name }}</div>
                        <div style="font-size:12px; color:#64748b; margin-top:2px;">
                            Trigger: <b>{{ ucfirst(str_replace('_', ' ', $wf->trigger_type)) }}</b> · Business: {{ $wf->business->name ?? 'All Workspaces' }}
                        </div>
                    </div>
                    <div style="display:flex; gap:6px;">
                        <button
                            wire:click="runWorkflow({{ $wf->id }})"
                            style="padding:6px 12px; background:#3978f6; color:white; border:0; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer;"
                        >
                            ▶ Run Test
                        </button>
                        <button
                            wire:click="toggleWorkflow({{ $wf->id }})"
                            style="padding:6px 10px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:6px; font-size:12px; cursor:pointer;"
                        >
                            {{ $wf->is_active ? 'Pause' : 'Activate' }}
                        </button>
                    </div>
                </div>
            @empty
                <div style="text-align:center; padding:30px; color:#94a3b8; font-size:13px;">
                    No custom workflows created yet. Create one using the form on the right.
                </div>
            @endforelse
        </div>

        <!-- Right: Create Workflow Form -->
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
            <h3 style="font-size:16px; font-weight:700; margin:0 0 12px; color:#0f172a;">Create New Workflow</h3>
            <form wire:submit="createWorkflow">
                <div style="margin-bottom:12px;">
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Workflow Name *</label>
                    <input wire:model="name" type="text" placeholder="e.g. Inbound Enquiry Qualification" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:8px 10px; font-size:12.5px;">
                    @error('name') <span style="color:#dc2626; font-size:11px;">{{ $message }}</span> @enderror
                </div>

                <div style="margin-bottom:12px;">
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Business Scope *</label>
                    <select wire:model="business_id" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:8px 10px; font-size:12.5px; background:white;">
                        @foreach($businesses as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Trigger *</label>
                    <select wire:model="trigger_type" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:8px 10px; font-size:12.5px; background:white;">
                        <option value="incoming_message">Incoming WhatsApp Message</option>
                        <option value="new_contact">New Contact Detected</option>
                        <option value="callback_requested">Customer Requests Callback</option>
                        <option value="call_completed">Voice Call Completed</option>
                        <option value="human_takeover">Human Handover Triggered</option>
                    </select>
                </div>

                <button type="submit" style="width:100%; background:#3978f6; color:white; border:0; border-radius:7px; padding:9px; font-size:13px; font-weight:700; cursor:pointer;">
                    Save Workflow
                </button>
            </form>
        </div>
    </div>
</div>

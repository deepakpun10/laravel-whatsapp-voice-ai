<div>
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:22px; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 style="font-size:24px; font-weight:800; margin:0 0 6px; color:#0f172a;">Business Settings</h1>
            <p style="margin:0; color:#64748b; font-size:13.5px;">Manage shared phone line safeguards, operating hours, and tenant preferences.</p>
        </div>
    </div>

    <form wire:submit="saveSettings">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
            <!-- Left: General Preferences -->
            <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:22px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
                <h3 style="font-size:15px; font-weight:700; margin:0 0 14px; color:#0f172a;">Operating Configuration</h3>

                <div style="margin-bottom:14px;">
                    <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:4px;">Workspace *</label>
                    <select wire:model.live="business_id" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:8px 10px; font-size:12.5px; background:white;">
                        @foreach($businesses as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:4px;">Timezone</label>
                    <input wire:model="timezone" type="text" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:8px 10px; font-size:12.5px;">
                </div>

                <div style="margin-bottom:14px;">
                    <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:4px;">Human Handover Destination</label>
                    <input wire:model="handover_destination" type="text" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:8px 10px; font-size:12.5px;">
                </div>

                <div>
                    <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:4px;">Unknown Query Policy</label>
                    <select wire:model="unknown_query_behavior" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:8px 10px; font-size:12.5px; background:white;">
                        <option value="ask_clarification">Ask neutral clarification / Offer callback</option>
                        <option value="human_handover">Immediate human rep takeover</option>
                    </select>
                </div>
            </div>

            <!-- Right: Shared Number Safety Rules matching prototype -->
            <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:22px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
                <h3 style="font-size:15px; font-weight:700; margin:0 0 6px; color:#0f172a;">Shared Line Cross-Talk Safeguards</h3>
                <p style="color:#64748b; font-size:12px; margin:0 0 16px;">
                    Mandatory security protocols that strictly prevent sending Himachal Garlic products or pricing to Pundeer Technologies customers and vice-versa.
                </p>

                <div style="display:flex; flex-direction:column; gap:12px; font-size:13px; color:#334155;">
                    <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                        <input wire:model="rule_keep_existing_assignment" type="checkbox" style="width:17px; height:17px;">
                        <span>Keep existing contact-business assignment locked</span>
                    </label>

                    <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                        <input wire:model="rule_require_confidence" type="checkbox" style="width:17px; height:17px;">
                        <span>Require minimum confidence threshold (0.85) for new enquiries</span>
                    </label>

                    <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                        <input wire:model="rule_block_ambiguous_replies" type="checkbox" style="width:17px; height:17px;">
                        <span>Block business-specific replies when route is ambiguous</span>
                    </label>

                    <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                        <input wire:model="rule_block_calls_without_route" type="checkbox" style="width:17px; height:17px;">
                        <span>Block AI outbound calls unless destination business is confirmed</span>
                    </label>
                </div>
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end;">
            <button
                type="submit"
                style="background:#3978f6; color:white; border:0; border-radius:8px; padding:10px 22px; font-size:13px; font-weight:700; cursor:pointer;"
            >
                Save Settings
            </button>
        </div>
    </form>
</div>

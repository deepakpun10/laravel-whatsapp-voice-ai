<div style="max-width:700px; margin:0 auto;">
    <div style="margin-bottom:20px;">
        <a href="{{ route('admin.calls.index') }}" style="color:#3978f6; font-size:13px; text-decoration:none; font-weight:600;">
            ← Back to Calls
        </a>
        <h1 style="font-size:24px; font-weight:800; margin:8px 0 4px; color:#0f172a;">Initiate Voice Call</h1>
        <p style="margin:0; color:#64748b; font-size:13.5px;">Trigger an automated outbound voice call via Sarvam AI voice telephony gateway.</p>
    </div>

    <form wire:submit="initiateCall" style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:26px; box-shadow:0 8px 24px rgba(0,0,0,.04);">
        <div style="margin-bottom:16px;">
            <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                Destination Business Workspace *
            </label>
            <select
                wire:model="business_id"
                style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px; background:white;"
            >
                @foreach($businesses as $b)
                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                @endforeach
            </select>
            @error('business_id') <span style="color:#dc2626; font-size:11.5px;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                Recipient Phone Number *
            </label>
            <input
                wire:model="to_number"
                type="text"
                placeholder="+91..."
                style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px;"
            >
            @error('to_number') <span style="color:#dc2626; font-size:11.5px;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom:20px;">
            <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                Call Type *
            </label>
            <select
                wire:model="call_type"
                style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px; background:white;"
            >
                <option value="ai_agent">AI Voice Agent (Conversational)</option>
                <option value="callback">Callback Request</option>
                <option value="human">Human Rep Connect</option>
            </select>
        </div>

        <!-- Compliance & Consent Safeguard -->
        <div style="background:#fffbeb; border:1px solid #fef3c7; border-radius:8px; padding:12px; margin-bottom:20px;">
            <label style="display:flex; align-items:flex-start; gap:8px; font-size:12px; color:#92400e; cursor:pointer;">
                <input wire:model="has_consent" type="checkbox" style="width:16px; height:16px; margin-top:2px;">
                <span>
                    <b>Regulatory Consent Verification</b>: I confirm this recipient has requested or consented to receive automated business communications from this business workspace in accordance with TRAI regulations.
                </span>
            </label>
            @error('has_consent') <span style="color:#dc2626; font-size:11.5px; display:block; margin-top:4px;">{{ $message }}</span> @enderror
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <a href="{{ route('admin.calls.index') }}" style="padding:9px 16px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none;">
                Cancel
            </a>
            <button
                type="submit"
                style="padding:9px 20px; border:0; background:#3978f6; color:white; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;"
            >
                Place Voice Call
            </button>
        </div>
    </form>
</div>

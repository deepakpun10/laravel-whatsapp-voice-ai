<div style="max-width:900px; margin:0 auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div style="margin-bottom:20px; display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px;">
        <div>
            <a href="{{ route('admin.calls.index') }}" style="color:#3978f6; font-size:13px; text-decoration:none; font-weight:600;">
                ← Back to Voice Calls
            </a>
            <h1 style="font-size:24px; font-weight:800; margin:6px 0 4px; color:#0f172a;">Call Log Details</h1>
            <p style="margin:0; color:#64748b; font-size:13.5px;">
                Recipient: <b>{{ $call->to_number }}</b> · Business: <b>{{ $call->business->name ?? 'Unassigned' }}</b> · Agent: {{ $call->aiAgent->name ?? 'Default Sarvam Agent' }}
            </p>
        </div>
        <div style="display:flex; gap:10px;">
            <button
                type="button"
                wire:click="retryCall"
                style="background:#3978f6; color:white; border:none; padding:8px 16px; border-radius:8px; font-size:12.5px; font-weight:700; cursor:pointer;"
            >
                📞 Call Again (Retry)
            </button>
            @if($call->contact)
                <a
                    href="{{ route('admin.contacts.show', $call->contact) }}"
                    style="background:#f1f5f9; color:#334155; border:1px solid #cbd5e1; padding:8px 14px; border-radius:8px; font-size:12.5px; font-weight:600; text-decoration:none;"
                >
                    View Contact Profile →
                </a>
            @endif
        </div>
    </div>

    <!-- Metadata Grid -->
    <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:22px; box-shadow:0 6px 20px rgba(0,0,0,.03); margin-bottom:20px;">
        <h3 style="font-size:15px; font-weight:700; margin:0 0 14px; color:#0f172a;">Call Telephony Telemetry</h3>
        <div style="display:grid; grid-template-columns:repeat(6, 1fr); gap:12px; font-size:12.5px; border-bottom:1px solid #f1f5f9; padding-bottom:14px; margin-bottom:16px;">
            <div>
                <div style="color:#64748b; font-size:11px;">Call Start Time</div>
                <div style="font-weight:700; margin-top:2px; font-size:12px; color:#0f172a;">
                    {{ $call->started_at ? $call->started_at->timezone('Asia/Kolkata')->format('d M Y, h:i A') : $call->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}
                </div>
            </div>
            <div>
                <div style="color:#64748b; font-size:11px;">Duration</div>
                <div style="font-weight:700; font-family:monospace; margin-top:2px;">
                    {{ gmdate('i:s', $call->duration_seconds) }}
                    @if($call->duration_seconds > 0)
                        <span style="font-size:11px; color:#16a34a; font-family:sans-serif;">({{ $call->duration_seconds }}s)</span>
                    @endif
                </div>
            </div>
            <div>
                <div style="color:#64748b; font-size:11px;">Direction</div>
                <div style="font-weight:700; text-transform:capitalize; margin-top:2px;">{{ $call->direction }}</div>
            </div>
            <div>
                <div style="color:#64748b; font-size:11px;">Status</div>
                <div style="font-weight:700; color:#16a34a; margin-top:2px;">{{ ucfirst($call->status) }}</div>
            </div>
            <div>
                <div style="color:#64748b; font-size:11px;">Outcome</div>
                <div style="font-weight:700; color:#2563eb; margin-top:2px;">{{ str_replace('_', ' ', $call->outcome ?: 'Pending Review') }}</div>
            </div>
            <div>
                <div style="color:#64748b; font-size:11px;">Caller Line</div>
                <div style="font-weight:700; font-family:monospace; margin-top:2px;">{{ $call->from_number ?: 'Sarvam Line' }}</div>
            </div>
        </div>

        @if($call->recording_url)
            <div style="margin-bottom:16px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px;">
                <div style="font-weight:700; font-size:12px; color:#334155; margin-bottom:6px;">🎙️ Call Audio Recording</div>
                <audio controls style="width:100%; height:36px;">
                    <source src="{{ $call->recording_url }}" type="audio/mpeg">
                    Your browser does not support audio element.
                </audio>
            </div>
        @endif

        @if($call->summary)
            <div style="margin-bottom:16px; background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:12px 14px;">
                <div style="font-weight:700; font-size:12px; color:#1e40af; margin-bottom:4px;">🧠 AI Call Summary & Key Takeaways</div>
                <div style="font-size:12.5px; color:#1e293b; line-height:1.5;">{{ $call->summary }}</div>
            </div>
        @endif

        @if(!empty($call->extracted_facts))
            <div style="margin-bottom:16px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:12px 14px;">
                <div style="font-weight:700; font-size:12px; color:#166534; margin-bottom:6px;">📊 Extracted Customer Requirements</div>
                <div style="display:flex; gap:16px; flex-wrap:wrap; font-size:12px;">
                    @foreach($call->extracted_facts as $k => $v)
                        <div><b>{{ ucwords(str_replace('_', ' ', $k)) }}:</b> <span style="color:#15803d; font-weight:700;">{{ $v }}</span></div>
                    @endforeach
                </div>
            </div>
        @endif

        <h3 style="font-size:14px; font-weight:700; margin:0 0 8px; color:#0f172a;">Full Conversation Transcript</h3>
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:14px; font-size:12.5px; line-height:1.6; color:#334155; white-space:pre-wrap; max-height:300px; overflow-y:auto;">
{{ $call->transcript ?: 'No transcript recorded for this session.' }}
        </div>
    </div>

    <!-- Update Outcome Buttons -->
    <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:18px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
        <h3 style="font-size:14px; font-weight:700; margin:0 0 10px; color:#0f172a;">Update CRM Call Outcome</h3>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <button
                wire:click="updateOutcome('interested')"
                style="padding:6px 14px; border:1px solid #86efac; background:#f0fdf4; color:#15803d; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer;"
            >
                ✓ Interested Customer
            </button>
            <button
                wire:click="updateOutcome('callback_requested')"
                style="padding:6px 14px; border:1px solid #fde68a; background:#fefce8; color:#a16207; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer;"
            >
                ◷ Callback Requested
            </button>
            <button
                wire:click="updateOutcome('human_handover')"
                style="padding:6px 14px; border:1px solid #fed7aa; background:#fff7ed; color:#c2410c; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer;"
            >
                👤 Human Handover Needed
            </button>
            <button
                wire:click="updateOutcome('not_interested')"
                style="padding:6px 14px; border:1px solid #fecaca; background:#fef2f2; color:#b91c1c; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer;"
            >
                ✕ Not Interested
            </button>
        </div>
    </div>
</div>

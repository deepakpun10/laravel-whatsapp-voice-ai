<div style="padding: 24px 28px; max-width: 1100px; margin: 0 auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <!-- Top Header -->
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 style="font-size:24px; font-weight:800; margin:0 0 6px; color:#0f172a;">⚙️ Voice Calling Settings & Compliance</h1>
            <p style="margin:0; color:#64748b; font-size:13.5px;">Configure automated calling schedules, retry limits, qualification thresholds, and telecom consent safety.</p>
        </div>
        <div style="display:flex; gap:10px;">
            <button
                type="button"
                wire:click="toggleEmergencyPause"
                style="padding:9px 18px; border-radius:8px; font-weight:700; font-size:13px; cursor:pointer; border:none; {{ $emergency_pause ? 'background:#dc2626; color:white;' : 'background:#fee2e2; color:#991b1b;' }}"
            >
                {{ $emergency_pause ? '⚠️ Emergency Pause Active (Click to Resume)' : '🛑 Emergency Pause' }}
            </button>
            <button
                type="button"
                wire:click="saveSettings"
                style="background:#3978f6; color:white; border:none; padding:9px 20px; border-radius:8px; font-weight:700; font-size:13px; cursor:pointer;"
            >
                Save Settings
            </button>
        </div>
    </div>

    <!-- Business Selector -->
    <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:22px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <span style="font-size:13px; font-weight:700; color:#334155;">Settings Scope:</span>
            <select wire:model.live="businessFilter" style="border:1px solid #cbd5e1; border-radius:8px; padding:8px 12px; font-size:13px; background:white;">
                <option value="">Global Default Settings</option>
                @foreach($businesses as $b)
                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="font-size:12px; color:#64748b;">
            Timezone: <b>{{ $timezone }}</b>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
        <!-- Card 1: Channel Switches -->
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 4px 12px rgba(0,0,0,.02);">
            <div style="font-weight:700; font-size:15px; color:#0f172a; margin-bottom:14px; border-bottom:1px solid #f1f5f9; padding-bottom:8px;">
                1. General Calling Permissions
            </div>
            <div style="display:flex; flex-direction:column; gap:12px;">
                <label style="display:flex; align-items:center; justify-content:space-between; padding:10px; border:1px solid #e2e8f0; border-radius:8px; cursor:pointer;">
                    <div>
                        <div style="font-weight:700; font-size:13px; color:#0f172a;">Enable Automated AI Outbound Calls</div>
                        <div style="font-size:11.5px; color:#64748b;">When off, AI will never dial out automatically from workflows.</div>
                    </div>
                    <input wire:model="is_automated_calling_enabled" type="checkbox" style="width:18px; height:18px;">
                </label>

                <label style="display:flex; align-items:center; justify-content:space-between; padding:10px; border:1px solid #e2e8f0; border-radius:8px; cursor:pointer;">
                    <div>
                        <div style="font-weight:700; font-size:13px; color:#0f172a;">Enable Inbound Voice Calling</div>
                        <div style="font-size:11.5px; color:#64748b;">Allow incoming customer calls to be answered by Sarvam Voice Agent.</div>
                    </div>
                    <input wire:model="inbound_enabled" type="checkbox" style="width:18px; height:18px;">
                </label>

                <label style="display:flex; align-items:center; justify-content:space-between; padding:10px; border:1px solid #e2e8f0; border-radius:8px; cursor:pointer;">
                    <div>
                        <div style="font-weight:700; font-size:13px; color:#0f172a;">Enable Outbound Voice Calling</div>
                        <div style="font-size:11.5px; color:#64748b;">Allow agents and callbacks to dial customer numbers.</div>
                    </div>
                    <input wire:model="outbound_enabled" type="checkbox" style="width:18px; height:18px;">
                </label>
            </div>
        </div>

        <!-- Card 2: Schedule & Permitted Hours -->
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 4px 12px rgba(0,0,0,.02);">
            <div style="font-weight:700; font-size:15px; color:#0f172a; margin-bottom:14px; border-bottom:1px solid #f1f5f9; padding-bottom:8px;">
                2. Calling Schedule (TRAI Compliance)
            </div>
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:6px;">Permitted Calling Hours</label>
                <div style="display:flex; gap:10px; align-items:center;">
                    <input wire:model="start_time" type="time" style="flex:1; border:1px solid #cbd5e1; border-radius:8px; padding:8px 10px; font-size:13px;">
                    <span style="color:#64748b; font-weight:700;">to</span>
                    <input wire:model="end_time" type="time" style="flex:1; border:1px solid #cbd5e1; border-radius:8px; padding:8px 10px; font-size:13px;">
                </div>
                <div style="font-size:11px; color:#64748b; margin-top:4px;">Calls outside these hours will be queued or skipped to prevent disturbance.</div>
            </div>

            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:6px;">Allowed Days</label>
                <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:8px;">
                    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $d)
                        <label style="display:flex; align-items:center; gap:6px; font-size:12px; font-weight:600; color:#334155; cursor:pointer;">
                            <input type="checkbox" wire:model="allowed_days" value="{{ $d }}">
                            {{ ucfirst(substr($d, 0, 3)) }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Card 3: Lead Qualification & Thresholds -->
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 4px 12px rgba(0,0,0,.02);">
            <div style="font-weight:700; font-size:15px; color:#0f172a; margin-bottom:14px; border-bottom:1px solid #f1f5f9; padding-bottom:8px;">
                3. Lead Qualification Thresholds
            </div>
            <div style="display:flex; flex-direction:column; gap:12px;">
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:4px;">Minimum Lead Score (0 - 100)</label>
                    <input wire:model="min_lead_score" type="number" min="0" max="100" style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:8px 10px; font-size:13px; box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:4px;">Minimum Estimated Budget (₹)</label>
                    <input wire:model="min_budget" type="number" min="0" step="500" style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:8px 10px; font-size:13px; box-sizing:border-box;">
                </div>
                <label style="display:flex; align-items:center; gap:8px; font-size:12.5px; font-weight:600; color:#334155; cursor:pointer;">
                    <input wire:model="require_explicit_consent" type="checkbox">
                    Require verified callback consent before automated calling
                </label>
            </div>
        </div>

        <!-- Card 4: Retry Policy -->
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 4px 12px rgba(0,0,0,.02);">
            <div style="font-weight:700; font-size:15px; color:#0f172a; margin-bottom:14px; border-bottom:1px solid #f1f5f9; padding-bottom:8px;">
                4. Retry Policy & Spam Prevention
            </div>
            <div style="display:flex; flex-direction:column; gap:12px;">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:4px;">Max Daily Attempts</label>
                        <input wire:model="max_retry_attempts" type="number" min="1" max="5" style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:8px 10px; font-size:13px; box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:4px;">Retry Interval (Mins)</label>
                        <input wire:model="retry_interval_minutes" type="number" min="15" max="720" style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:8px 10px; font-size:13px; box-sizing:border-box;">
                    </div>
                </div>

                <label style="display:flex; align-items:center; gap:8px; font-size:12px; font-weight:600; color:#334155; cursor:pointer;">
                    <input wire:model="retry_on_busy" type="checkbox">
                    Retry if line is busy
                </label>
                <label style="display:flex; align-items:center; gap:8px; font-size:12px; font-weight:600; color:#334155; cursor:pointer;">
                    <input wire:model="retry_on_no_answer" type="checkbox">
                    Retry if customer does not answer
                </label>
                <label style="display:flex; align-items:center; gap:8px; font-size:12px; font-weight:600; color:#334155; cursor:pointer;">
                    <input wire:model="stop_on_decline" type="checkbox">
                    Permanently stop if customer declines or requests not to call
                </label>
            </div>
        </div>
    </div>
</div>

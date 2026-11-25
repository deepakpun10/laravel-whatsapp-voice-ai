<div>
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <div>
            <h1 style="font-size:22px; font-weight:800; margin:0 0 4px; color:#0f172a;">Edit Interactive Template: {{ $template->name }}</h1>
            <p style="margin:0; color:#64748b; font-size:13px;">Modify button options, list rows, triggers, and content.</p>
        </div>
        <a href="{{ route('admin.interactive.show', $template) }}" style="color:#64748b; text-decoration:none; font-size:13px; font-weight:600;">
            ← Back to View Details
        </a>
    </div>

    <div style="display:grid; grid-template-columns:1.4fr 1fr; gap:24px; align-items:flex-start;">
        <!-- Left: Edit Form -->
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:22px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
            <form wire:submit="updateTemplate">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:4px;">Business *</label>
                        <select wire:model.live="business_id" style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:8px 12px; font-size:13px; background:white;">
                            @foreach($businesses as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:4px;">Template Name *</label>
                        <input wire:model="name" type="text" style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:8px 12px; font-size:13px;">
                        @error('name') <span style="color:#dc2626; font-size:11px;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Header (Optional) -->
                <div style="margin-bottom:12px;">
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Header Text (Optional, max 60 chars)</label>
                    <input wire:model.live="header_text" maxlength="60" type="text" style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:7px 12px; font-size:12.5px;">
                </div>

                <!-- Body (Required) -->
                <div style="margin-bottom:12px;">
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Message Body Text * (max 1024 chars)</label>
                    <textarea wire:model.live="body_text" rows="3" maxlength="1024" style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:8px 12px; font-size:13px;"></textarea>
                    @error('body_text') <span style="color:#dc2626; font-size:11px;">{{ $message }}</span> @enderror
                </div>

                <!-- Footer (Optional) -->
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Footer Text (Optional, max 60 chars)</label>
                    <input wire:model.live="footer_text" maxlength="60" type="text" style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:7px 12px; font-size:12.5px;">
                </div>

                <!-- BUILDER: REPLY BUTTONS -->
                @if($interactive_type === 'button')
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:16px; margin-bottom:18px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                            <b style="font-size:13px; color:#0f172a;">Reply Buttons (Max 3)</b>
                            @if(count($buttons) < 3)
                                <button type="button" wire:click="addButton" style="background:#3978f6; color:white; border:none; padding:4px 10px; border-radius:6px; font-size:11.5px; font-weight:700; cursor:pointer;">
                                    + Add Button
                                </button>
                            @endif
                        </div>

                        @foreach($buttons as $bIdx => $btn)
                            <div style="display:grid; grid-template-columns:1.5fr 1fr 1fr auto; gap:8px; align-items:center; margin-bottom:8px; background:white; padding:8px; border-radius:6px; border:1px solid #cbd5e1;">
                                <div>
                                    <input wire:model.live="buttons.{{ $bIdx }}.title" maxlength="20" type="text" placeholder="Button Title" style="width:100%; border:1px solid #e2e8f0; border-radius:5px; padding:6px 8px; font-size:12px;">
                                </div>
                                <div>
                                    <input wire:model="buttons.{{ $bIdx }}.button_id" type="text" placeholder="ID" style="width:100%; border:1px solid #e2e8f0; border-radius:5px; padding:6px 8px; font-size:11px;">
                                </div>
                                <div>
                                    <select wire:model="buttons.{{ $bIdx }}.action_type" style="width:100%; border:1px solid #e2e8f0; border-radius:5px; padding:6px 8px; font-size:11.5px; background:white;">
                                        <option value="set_service">Set Service</option>
                                        <option value="set_budget">Set Budget</option>
                                        <option value="ai_instruction">AI Instruction</option>
                                        <option value="callback_request">Request Callback</option>
                                    </select>
                                </div>
                                <div>
                                    @if(count($buttons) > 1)
                                        <button type="button" wire:click="removeButton({{ $bIdx }})" style="background:#fee2e2; color:#dc2626; border:1px solid #fca5a5; padding:5px 8px; border-radius:5px; font-size:11px; cursor:pointer;">✕</button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- BUILDER: LIST MESSAGE -->
                @if($interactive_type === 'list')
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:16px; margin-bottom:18px;">
                        <div style="margin-bottom:12px;">
                            <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">List Menu Button Label (Max 20 chars)</label>
                            <input wire:model.live="list_button_label" maxlength="20" type="text" style="width:100%; border:1px solid #cbd5e1; border-radius:6px; padding:7px 10px; font-size:12.5px;">
                        </div>

                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                            <b style="font-size:13px; color:#0f172a;">List Sections & Rows (Max 10 total)</b>
                            <button type="button" wire:click="addSection" style="background:#7c3aed; color:white; border:none; padding:4px 10px; border-radius:6px; font-size:11.5px; font-weight:700; cursor:pointer;">
                                + Add Section
                            </button>
                        </div>

                        @foreach($sections as $sIdx => $sec)
                            <div style="background:white; border:1px solid #cbd5e1; border-radius:8px; padding:12px; margin-bottom:12px;">
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                                    <input wire:model.live="sections.{{ $sIdx }}.title" maxlength="24" type="text" placeholder="Section Title" style="font-weight:700; font-size:12.5px; border:1px solid #e2e8f0; border-radius:5px; padding:5px 8px; width:70%;">
                                    <button type="button" wire:click="addRow({{ $sIdx }})" style="background:#ecfdf5; color:#16a34a; border:1px solid #bbf7d0; padding:4px 8px; border-radius:5px; font-size:11px; font-weight:700; cursor:pointer;">
                                        + Row
                                    </button>
                                </div>

                                @foreach($sec['rows'] ?? [] as $rIdx => $row)
                                    <div style="display:grid; grid-template-columns:1fr 1fr auto; gap:6px; margin-bottom:6px; align-items:center;">
                                        <input wire:model.live="sections.{{ $sIdx }}.rows.{{ $rIdx }}.title" maxlength="24" type="text" placeholder="Row Title" style="border:1px solid #e2e8f0; border-radius:5px; padding:5px 8px; font-size:11.5px;">
                                        <input wire:model.live="sections.{{ $sIdx }}.rows.{{ $rIdx }}.description" maxlength="72" type="text" placeholder="Description" style="border:1px solid #e2e8f0; border-radius:5px; padding:5px 8px; font-size:11.5px;">
                                        <button type="button" wire:click="removeRow({{ $sIdx }}, {{ $rIdx }})" style="color:#dc2626; background:none; border:none; cursor:pointer; font-size:13px;">✕</button>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endif

                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-top:1px solid #f1f5f9; padding-top:14px;">
                    <label style="display:flex; align-items:center; gap:8px; font-size:12.5px; font-weight:600; color:#334155; cursor:pointer;">
                        <input wire:model="ai_availability" type="checkbox" style="width:16px; height:16px;">
                        Allow AI Agents to choose this template dynamically
                    </label>

                    <button type="submit" style="background:#3978f6; color:white; border:none; padding:10px 24px; border-radius:8px; font-weight:700; font-size:13px; cursor:pointer;">
                        Update Template
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Real-time Smartphone Mockup Preview -->
        <div>
            <div style="position:sticky; top:20px;">
                <div style="font-size:12px; font-weight:700; color:#64748b; margin-bottom:8px; text-transform:uppercase;">
                    📱 Live Smartphone Preview
                </div>

                <div style="background:#0b141a; border:8px solid #1e293b; border-radius:36px; padding:16px 12px; box-shadow:0 20px 40px rgba(0,0,0,0.25); max-width:340px; margin:0 auto; color:white;">
                    <div style="width:100px; height:12px; background:#1e293b; border-radius:0 0 10px 10px; margin:-16px auto 14px;"></div>
                    <div style="display:flex; align-items:center; gap:10px; padding-bottom:10px; border-bottom:1px solid #1f2c34;">
                        <div style="width:34px; height:34px; border-radius:50%; background:#20c659; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:14px; color:white;">P</div>
                        <div>
                            <div style="font-weight:700; font-size:13px; color:#e9edef;">
                                {{ \App\Models\Business::find($business_id)?->name ?? $template->business->name }}
                            </div>
                            <div style="font-size:10px; color:#8696a0;">● Verified WhatsApp Business</div>
                        </div>
                    </div>

                    <div style="min-height:340px; background:#0b141a; background-image:radial-gradient(#1f2c34 1px, transparent 1px); background-size:16px 16px; padding:16px 6px; display:flex; flex-direction:column; justify-content:flex-end;">
                        <div style="background:#202c33; border-radius:10px; overflow:hidden; max-width:280px; box-shadow:0 2px 5px rgba(0,0,0,0.3); border-top:3px solid #25d366;">
                            <div style="padding:10px 12px;">
                                @if(!empty($header_text))
                                    <div style="font-weight:700; font-size:13px; color:#e9edef; margin-bottom:4px;">{{ $header_text }}</div>
                                @endif
                                <div style="font-size:12.5px; color:#d1d7db; line-height:1.45; white-space:pre-wrap;">{{ $body_text }}</div>
                                @if(!empty($footer_text))
                                    <div style="font-size:10px; color:#8696a0; margin-top:6px;">{{ $footer_text }}</div>
                                @endif
                                <div style="text-align:right; font-size:9.5px; color:#8696a0; margin-top:3px;">{{ now()->format('H:i') }} ✓✓</div>
                            </div>

                            @if($interactive_type === 'button')
                                <div style="border-top:1px solid #2a3942;">
                                    @foreach($buttons as $b)
                                        <div style="padding:9px; text-align:center; color:#53bdeb; font-size:12px; font-weight:600; border-bottom:1px solid #2a3942; background:#202c33;">
                                            🔘 {{ $b['title'] ?: 'Button' }}
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($interactive_type === 'list')
                                <div style="border-top:1px solid #2a3942; padding:9px; text-align:center; color:#53bdeb; font-size:12px; font-weight:600; background:#202c33;">
                                    ☰ {{ $list_button_label ?: 'Select Option' }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

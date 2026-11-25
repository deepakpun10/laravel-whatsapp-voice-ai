<div>
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
        <div>
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
                <h1 style="font-size:22px; font-weight:800; margin:0; color:#0f172a;">{{ $template->name }}</h1>
                <span style="font-size:11px; font-weight:700; padding:2px 8px; border-radius:10px; {{ $template->status === 'active' ? 'background:#ecfdf5; color:#16a34a;' : 'background:#fee2e2; color:#dc2626;' }}">
                    ● {{ ucfirst($template->status) }}
                </span>
                <span style="font-size:11px; font-weight:700; padding:2px 8px; border-radius:10px; background:#eff6ff; color:#2563eb;">
                    {{ strtoupper($template->interactive_type) }}
                </span>
            </div>
            <p style="margin:0; color:#64748b; font-size:13px;">Business: <b>{{ $template->business->name }}</b> | Category: <code>{{ $template->category }}</code></p>
        </div>
        <div style="display:flex; gap:8px;">
            <a href="{{ route('admin.interactive.edit', $template) }}" style="background:white; border:1px solid #cbd5e1; color:#334155; padding:8px 16px; border-radius:8px; font-weight:700; font-size:12.5px; text-decoration:none;">
                ✎ Edit Template
            </a>
            <button
                type="button"
                wire:click="toggleStatus"
                style="background:{{ $template->status === 'active' ? '#fff7ed' : '#ecfdf5' }}; border:1px solid {{ $template->status === 'active' ? '#ffedd5' : '#bbf7d0' }}; color:{{ $template->status === 'active' ? '#c2410c' : '#15803d' }}; padding:8px 14px; border-radius:8px; font-weight:700; font-size:12.5px; cursor:pointer;"
            >
                {{ $template->status === 'active' ? 'Deactivate' : 'Activate' }}
            </button>
            <a href="{{ route('admin.interactive.index') }}" style="background:#f1f5f9; color:#475569; padding:8px 14px; border-radius:8px; font-weight:600; font-size:12.5px; text-decoration:none;">
                ← Back
            </a>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1.4fr 1fr; gap:24px; align-items:flex-start;">
        <!-- Left Column: Details, Send Test, Analytics -->
        <div style="display:flex; flex-direction:column; gap:20px;">
            <!-- Send Test Message Box -->
            <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:14px; padding:18px;">
                <b style="font-size:14px; color:#166534; display:block; margin-bottom:4px;">📲 Send Real Test to WhatsApp Phone</b>
                <p style="font-size:12px; color:#15803d; margin:0 0 12px; line-height:1.45;">
                    Verify this interactive message on your real phone using Meta WhatsApp Cloud API.
                </p>
                <div style="display:flex; gap:10px;">
                    <input
                        wire:model="test_phone_number"
                        type="text"
                        placeholder="e.g. 919876543210 (with country code)"
                        style="flex:1; border:1px solid #86efac; border-radius:8px; padding:8px 12px; font-size:13px; background:white;"
                    >
                    <button
                        type="button"
                        wire:click="sendTestMessage"
                        style="background:#16a34a; color:white; border:none; padding:8px 18px; border-radius:8px; font-weight:700; font-size:13px; cursor:pointer;"
                    >
                        Send Test Now
                    </button>
                </div>
                @if($testResult)
                    <div style="margin-top:10px; font-size:12px; font-weight:600; color:{{ str_contains($testResult, '✓') ? '#166534' : '#dc2626' }};">
                        {{ $testResult }}
                    </div>
                @endif
            </div>

            <!-- Content Specs Card -->
            <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
                <h3 style="font-size:15px; font-weight:700; margin:0 0 12px; color:#0f172a;">Template Message Content</h3>
                
                @if($template->header_text)
                    <div style="margin-bottom:10px;">
                        <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase;">Header:</span>
                        <div style="font-weight:700; font-size:13px; color:#0f172a;">{{ $template->header_text }}</div>
                    </div>
                @endif

                <div style="margin-bottom:10px;">
                    <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase;">Body:</span>
                    <div style="font-size:13px; color:#334155; line-height:1.5; background:#f8fafc; padding:10px 12px; border-radius:8px; border:1px solid #f1f5f9; white-space:pre-wrap;">{{ $template->body_text }}</div>
                </div>

                @if($template->footer_text)
                    <div style="margin-bottom:10px;">
                        <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase;">Footer:</span>
                        <div style="font-size:12px; color:#64748b;">{{ $template->footer_text }}</div>
                    </div>
                @endif
            </div>

            <!-- Buttons / Rows List -->
            <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
                <h3 style="font-size:15px; font-weight:700; margin:0 0 12px; color:#0f172a;">Configured Options & Actions</h3>

                @if($template->isButtonType())
                    <table style="width:100%; border-collapse:collapse; font-size:12.5px; text-align:left;">
                        <thead>
                            <tr style="border-bottom:1px solid #e2e8f0; background:#f8fafc; color:#64748b;">
                                <th style="padding:8px 10px;">BUTTON TITLE</th>
                                <th style="padding:8px 10px;">BUTTON ID</th>
                                <th style="padding:8px 10px;">ACTION TYPE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($template->buttons as $b)
                                <tr style="border-bottom:1px solid #f1f5f9;">
                                    <td style="padding:10px; font-weight:700; color:#0f172a;">🔘 {{ $b->title }}</td>
                                    <td style="padding:10px;"><code>{{ $b->button_id }}</code></td>
                                    <td style="padding:10px; text-transform:capitalize;">{{ str_replace('_', ' ', $b->action_type) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @elseif($template->isListType())
                    @foreach($template->sections as $sec)
                        <div style="margin-bottom:12px;">
                            <div style="font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">SECTION: {{ $sec->title }}</div>
                            <table style="width:100%; border-collapse:collapse; font-size:12px; text-align:left;">
                                <tbody>
                                    @foreach($sec->rows as $r)
                                        <tr style="border-bottom:1px solid #f1f5f9;">
                                            <td style="padding:8px; font-weight:700; color:#0f172a;">{{ $r->title }}</td>
                                            <td style="padding:8px; color:#64748b;">{{ $r->description }}</td>
                                            <td style="padding:8px;"><code>{{ $r->row_id }}</code></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Linked Automation Rules -->
            <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
                <h3 style="font-size:15px; font-weight:700; margin:0 0 10px; color:#0f172a;">Active Automation Rules</h3>
                @forelse($template->automationRules as $rule)
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px; margin-bottom:8px; font-size:12.5px; display:flex; justify-content:space-between;">
                        <div>
                            <b>{{ $rule->name }}</b>
                            <span style="font-size:11px; color:#64748b;"> (Trigger: {{ $rule->trigger_type }})</span>
                        </div>
                        <span style="font-size:11px; font-weight:700; color:{{ $rule->is_active ? '#16a34a' : '#dc2626' }};">
                            {{ $rule->is_active ? 'Active' : 'Paused' }}
                        </span>
                    </div>
                @empty
                    <div style="font-size:12px; color:#94a3b8;">No automation rules linked to this template yet.</div>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Smartphone Mockup Preview -->
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
                            <div style="font-weight:700; font-size:13px; color:#e9edef;">{{ $template->business->name }}</div>
                            <div style="font-size:10px; color:#8696a0;">● Verified WhatsApp Business</div>
                        </div>
                    </div>

                    <div style="min-height:340px; background:#0b141a; background-image:radial-gradient(#1f2c34 1px, transparent 1px); background-size:16px 16px; padding:16px 6px; display:flex; flex-direction:column; justify-content:flex-end;">
                        <div style="background:#202c33; border-radius:10px; overflow:hidden; max-width:280px; box-shadow:0 2px 5px rgba(0,0,0,0.3); border-top:3px solid #25d366;">
                            <div style="padding:10px 12px;">
                                @if(!empty($template->header_text))
                                    <div style="font-weight:700; font-size:13px; color:#e9edef; margin-bottom:4px;">{{ $template->header_text }}</div>
                                @endif
                                <div style="font-size:12.5px; color:#d1d7db; line-height:1.45; white-space:pre-wrap;">{{ $template->body_text }}</div>
                                @if(!empty($template->footer_text))
                                    <div style="font-size:10px; color:#8696a0; margin-top:6px;">{{ $template->footer_text }}</div>
                                @endif
                                <div style="text-align:right; font-size:9.5px; color:#8696a0; margin-top:3px;">{{ now()->format('H:i') }} ✓✓</div>
                            </div>

                            @if($template->isButtonType())
                                <div style="border-top:1px solid #2a3942;">
                                    @foreach($template->buttons as $b)
                                        <div style="padding:9px; text-align:center; color:#53bdeb; font-size:12px; font-weight:600; border-bottom:1px solid #2a3942; background:#202c33;">
                                            🔘 {{ $b->title }}
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($template->isListType())
                                <div style="border-top:1px solid #2a3942; padding:9px; text-align:center; color:#53bdeb; font-size:12px; font-weight:600; background:#202c33;">
                                    ☰ {{ $template->list_button_label ?: 'Select Option' }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div>
    <!-- Dashboard Header -->
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:22px; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 style="font-size:24px; font-weight:800; margin:0 0 6px; color:#0f172a;">
                Good day, {{ auth()->user()?->name ?? 'Admin' }} 👋
            </h1>
            <p style="margin:0; color:#64748b; font-size:13.5px;">
                {{ $activeBusiness ? "Showing operations for {$activeBusiness->name}" : "Your multi-business AI sales operations at a glance (All Workspaces)" }}
            </p>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('admin.businesses.create') }}" style="background:white; border:1px solid #cbd5e1; color:#334155; padding:8px 14px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none;">
                ＋ Add Business
            </a>
            <a href="{{ route('admin.contacts.create') }}" style="background:#3978f6; color:white; padding:8px 16px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none;">
                ＋ Add Lead
            </a>
        </div>
    </div>

    <!-- 4 Stats Cards matching prototype -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:16px; margin-bottom:22px;">
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:18px; box-shadow:0 8px 24px rgba(0,0,0,.04); display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <div style="color:#64748b; font-size:12px; font-weight:600;">TOTAL CONVERSATIONS</div>
                <div style="font-size:28px; font-weight:800; color:#0f172a; margin:8px 0 4px;">{{ $totalConversations }}</div>
                <div style="font-size:11.5px; color:#16a34a; font-weight:600;">Active in MySQL</div>
            </div>
            <div style="width:42px; height:42px; border-radius:11px; background:#edf3ff; display:grid; place-items:center; font-size:18px; color:#3568ca;">
                💬
            </div>
        </div>

        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:18px; box-shadow:0 8px 24px rgba(0,0,0,.04); display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <div style="color:#64748b; font-size:12px; font-weight:600;">AI REPLIES SENT</div>
                <div style="font-size:28px; font-weight:800; color:#0f172a; margin:8px 0 4px;">{{ $aiRepliesSent }}</div>
                <div style="font-size:11.5px; color:#16a34a; font-weight:600;">Verified AI generation</div>
            </div>
            <div style="width:42px; height:42px; border-radius:11px; background:#edf3ff; display:grid; place-items:center; font-size:18px; color:#3568ca;">
                ✧
            </div>
        </div>

        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:18px; box-shadow:0 8px 24px rgba(0,0,0,.04); display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <div style="color:#64748b; font-size:12px; font-weight:600;">CALLS HANDLED</div>
                <div style="font-size:28px; font-weight:800; color:#0f172a; margin:8px 0 4px;">{{ $callsHandled }}</div>
                <div style="font-size:11.5px; color:#64748b; font-weight:600;">Voice Telephony</div>
            </div>
            <div style="width:42px; height:42px; border-radius:11px; background:#edf3ff; display:grid; place-items:center; font-size:18px; color:#3568ca;">
                ☎
            </div>
        </div>

        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:18px; box-shadow:0 8px 24px rgba(0,0,0,.04); display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <div style="color:#64748b; font-size:12px; font-weight:600;">QUALIFIED LEADS</div>
                <div style="font-size:28px; font-weight:800; color:#0f172a; margin:8px 0 4px;">{{ $qualifiedLeads }}</div>
                <div style="font-size:11.5px; color:#16a34a; font-weight:600;">Ready for conversion</div>
            </div>
            <div style="width:42px; height:42px; border-radius:11px; background:#edf3ff; display:grid; place-items:center; font-size:18px; color:#3568ca;">
                ♙
            </div>
        </div>
    </div>

    <!-- Middle Grid: Business Breakdown & Activity -->
    <div style="display:grid; grid-template-columns:1.5fr 1fr; gap:20px; margin-bottom:22px;">
        <!-- Left: Workspaces Breakdown -->
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
            <h3 style="font-size:15px; font-weight:700; margin:0 0 4px; color:#0f172a;">Active Business Workspaces</h3>
            <p style="color:#64748b; font-size:12.5px; margin:0 0 16px;">Real metrics calculated directly from database records.</p>

            @forelse($businesses as $b)
                <div style="margin-bottom:14px; border:1px solid #f1f5f9; border-radius:10px; padding:12px 14px; display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <a href="{{ route('admin.businesses.show', $b) }}" style="font-weight:700; color:#0f172a; text-decoration:none; font-size:13.5px;">
                            {{ $b->name }}
                        </a>
                        <div style="color:#64748b; font-size:11.5px; margin-top:2px;">
                            Code: {{ $b->code ?? 'N/A' }} · {{ $b->category ?? 'General' }}
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <span style="font-size:12px; font-weight:700; color:#3568ca;">{{ $b->contacts_count }} Leads</span>
                        <div style="font-size:11px; color:#64748b;">{{ $b->conversations_count }} Conversations</div>
                    </div>
                </div>
            @empty
                <p style="color:#94a3b8; font-size:13px;">No active businesses configured.</p>
            @endforelse
        </div>

        <!-- Right: Business Routing Protocol Safeguard Card -->
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03); display:flex; flex-direction:column; justify-content:space-between;">
            <div>
                <h3 style="font-size:15px; font-weight:700; margin:0 0 8px; color:#0f172a;">Shared Line Routing Health</h3>
                <p style="color:#64748b; font-size:12px; margin:0 0 16px; line-height:1.6;">
                    The system evaluates intent across <b>Pundeer Technologies</b> (Web/IT) and <b>Himachal Garlic</b> (Wholesale Trade) in real time.
                </p>

                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; margin-bottom:12px; font-size:12px;">
                    <div style="font-weight:700; color:#0f172a; margin-bottom:4px;">Rule 1: Known Contact Memory</div>
                    <div style="color:#64748b;">Enquiries from existing customers stay locked to their assigned business.</div>
                </div>

                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; margin-bottom:12px; font-size:12px;">
                    <div style="font-weight:700; color:#0f172a; margin-bottom:4px;">Rule 2: Intent Threshold (0.85)</div>
                    <div style="color:#64748b;">Ambiguous matches go to Unassigned Review; AI will NEVER guess a price.</div>
                </div>
            </div>

            <a href="{{ route('admin.routing.index') }}" style="background:#edf3ff; color:#3568ca; text-align:center; padding:9px; border-radius:8px; font-weight:700; font-size:12.5px; text-decoration:none;">
                Test Routing Simulator →
            </a>
        </div>
    </div>

    <!-- Recent Conversations Table -->
    <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <h3 style="font-size:15px; font-weight:700; margin:0; color:#0f172a;">Recent Conversations</h3>
            <a href="{{ route('admin.conversations.index') }}" style="font-size:12px; color:#3978f6; font-weight:700; text-decoration:none;">
                View All in Inbox →
            </a>
        </div>

        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; text-align:left; font-size:12.5px;">
                <thead>
                    <tr style="border-bottom:1px solid #e2e8f0; background:#f8fafc; color:#64748b;">
                        <th style="padding:10px 12px; font-weight:600;">CONTACT</th>
                        <th style="padding:10px 12px; font-weight:600;">BUSINESS ROUTE</th>
                        <th style="padding:10px 12px; font-weight:600;">MODE</th>
                        <th style="padding:10px 12px; font-weight:600;">STATUS</th>
                        <th style="padding:10px 12px; font-weight:600; text-align:right;">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentConversations as $conv)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:12px;">
                                <b>{{ $conv->contact->name ?? 'Unknown' }}</b>
                                <div style="color:#64748b; font-size:11px;">{{ $conv->contact->phone ?? 'No phone' }}</div>
                            </td>
                            <td style="padding:12px;">
                                <span style="background:#f1f5f9; color:#0f172a; padding:3px 8px; border-radius:4px; font-weight:600; font-size:11.5px;">
                                    {{ $conv->business->name ?? 'Unassigned Review' }}
                                </span>
                            </td>
                            <td style="padding:12px;">
                                @if($conv->isAiActive())
                                    <span style="background:#ecfdf5; color:#16a34a; font-weight:700; font-size:11px; padding:2px 7px; border-radius:12px;">● AI Active</span>
                                @else
                                    <span style="background:#fffbeb; color:#b45309; font-weight:700; font-size:11px; padding:2px 7px; border-radius:12px;">● Human Takeover</span>
                                @endif
                            </td>
                            <td style="padding:12px;">
                                <span style="font-weight:600; text-transform:capitalize;">{{ str_replace('_', ' ', $conv->status) }}</span>
                            </td>
                            <td style="padding:12px; text-align:right;">
                                <a href="{{ route('admin.conversations.index', ['conversation' => $conv->id]) }}" style="padding:4px 10px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:5px; font-size:11.5px; text-decoration:none; font-weight:600;">
                                    Open
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:30px; color:#94a3b8;">
                                No conversations yet. Inbound WhatsApp messages will automatically appear here.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Voice Calls & Telephony Intelligence -->
    <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03); margin-top:22px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <div>
                <h3 style="font-size:15px; font-weight:700; margin:0 0 2px; color:#0f172a;">Voice Calls & AI Intelligence</h3>
                <p style="margin:0; font-size:12px; color:#64748b;">Phone calls handled by Sarvam Voice AI with live transcript analysis, key takeaways & customer intent.</p>
            </div>
            <a href="{{ route('admin.calls.index') }}" style="font-size:12px; color:#3978f6; font-weight:700; text-decoration:none;">
                View All Calls →
            </a>
        </div>

        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; text-align:left; font-size:12.5px;">
                <thead>
                    <tr style="border-bottom:1px solid #e2e8f0; background:#f8fafc; color:#64748b;">
                        <th style="padding:10px 12px; font-weight:600;">CUSTOMER / NUMBER</th>
                        <th style="padding:10px 12px; font-weight:600;">BUSINESS</th>
                        <th style="padding:10px 12px; font-weight:600;">DURATION</th>
                        <th style="padding:10px 12px; font-weight:600;">OUTCOME / INTENT</th>
                        <th style="padding:10px 12px; font-weight:600;">AI SUMMARY & REQUIREMENTS</th>
                        <th style="padding:10px 12px; font-weight:600; text-align:right;">TRANSCRIPT</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentCalls as $call)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:12px;">
                                <b>{{ $call->contact->name ?? 'Customer' }}</b>
                                <div style="color:#64748b; font-size:11px;">{{ $call->to_number }}</div>
                            </td>
                            <td style="padding:12px;">
                                <span style="background:#f1f5f9; color:#0f172a; padding:3px 8px; border-radius:4px; font-weight:600; font-size:11.5px;">
                                    {{ $call->business->name ?? 'Unassigned' }}
                                </span>
                            </td>
                            <td style="padding:12px; font-family:monospace;">
                                {{ gmdate('i:s', $call->duration_seconds) }}
                            </td>
                            <td style="padding:12px;">
                                @php
                                    $outcomeColors = [
                                        'interested' => ['bg' => '#ecfdf5', 'text' => '#15803d'],
                                        'callback_requested' => ['bg' => '#fefce8', 'text' => '#a16207'],
                                        'meeting_scheduled' => ['bg' => '#eff6ff', 'text' => '#1d4ed8'],
                                        'human_handover' => ['bg' => '#fff7ed', 'text' => '#c2410c'],
                                        'completed' => ['bg' => '#f1f5f9', 'text' => '#334155'],
                                        'ringing' => ['bg' => '#fef3c7', 'text' => '#b45309'],
                                        'failed' => ['bg' => '#fef2f2', 'text' => '#b91c1c'],
                                    ];
                                    $style = $outcomeColors[$call->outcome ?: $call->status] ?? ['bg' => '#f1f5f9', 'text' => '#475569'];
                                @endphp
                                <span style="background:{{ $style['bg'] }}; color:{{ $style['text'] }}; font-weight:700; font-size:11px; padding:3px 8px; border-radius:12px; text-transform:capitalize;">
                                    {{ str_replace('_', ' ', $call->outcome ?: $call->status) }}
                                </span>
                            </td>
                            <td style="padding:12px; max-width:280px;">
                                @if($call->summary)
                                    <div style="font-size:12px; color:#1e293b; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                        {{ $call->summary }}
                                    </div>
                                @elseif($call->status === 'ringing')
                                    <span style="color:#d97706; font-size:11.5px; font-style:italic;">Call connected / processing...</span>
                                @elseif(!empty($call->extracted_facts))
                                    <div style="font-size:11.5px; color:#166534;">
                                        @foreach($call->extracted_facts as $k => $v)
                                            <span style="background:#f0fdf4; padding:2px 6px; border-radius:4px; margin-right:4px;">{{ $k }}: {{ $v }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="color:#94a3b8; font-size:11.5px;">Pending final webhook callback</span>
                                @endif
                            </td>
                            <td style="padding:12px; text-align:right;">
                                <a href="{{ route('admin.calls.show', $call) }}" style="padding:4px 10px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:5px; font-size:11.5px; text-decoration:none; font-weight:600; white-space:nowrap;">
                                    View Log & Audio →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:30px; color:#94a3b8;">
                                No voice calls recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

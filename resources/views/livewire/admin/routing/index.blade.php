<div>
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:22px; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 style="font-size:24px; font-weight:800; margin:0 0 6px; color:#0f172a;">Business Routing</h1>
            <p style="margin:0; color:#64748b; font-size:13.5px;">One WhatsApp number serves multiple businesses. Our routing engine directs each enquiry safely without cross-talk.</p>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('admin.routing.create') }}" style="background:#3978f6; color:white; padding:9px 16px; border-radius:9px; font-size:13px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                ＋ Add Routing Rule
            </a>
        </div>
    </div>

    <!-- Active Connected Shared Number Card -->
    <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03); margin-bottom:20px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:40px; height:40px; border-radius:10px; background:#edf3ff; display:grid; place-items:center; font-size:18px;">
                    ☏
                </div>
                <div>
                    <div style="font-weight:800; font-size:14px; color:#0f172a;">Shared Inbound Number: +91 9800000000</div>
                    <div style="color:#64748b; font-size:12px;">Connected WhatsApp Line serving multiple business workspaces</div>
                </div>
            </div>
            <span style="background:#ecfdf5; color:#16a34a; font-weight:700; font-size:11.5px; padding:4px 10px; border-radius:20px;">
                ● Active Shared Line
            </span>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:12px;">
            <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; background:#f8fafc;">
                <div style="font-weight:700; font-size:13px; color:#0f172a;">💻 Pundeer Technologies</div>
                <div style="color:#64748b; font-size:12px; margin-top:4px;">RAG: Company & Pricing Docs · Agent: Website Sales Assistant</div>
                <div style="margin-top:6px;"><span style="background:#edf3ff; color:#3568ca; font-size:10px; font-weight:700; padding:2px 6px; border-radius:4px;">ID: PUN-001</span></div>
            </div>
            <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; background:#f8fafc;">
                <div style="font-weight:700; font-size:13px; color:#0f172a;">🧄 Himachal Garlic</div>
                <div style="color:#64748b; font-size:12px; margin-top:4px;">RAG: Garlic Catalog & Wholesale Pricing · Agent: Garlic Trade Assistant</div>
                <div style="margin-top:6px;"><span style="background:#e7f8f8; color:#16818b; font-size:10px; font-weight:700; padding:2px 6px; border-radius:4px;">ID: GAR-002</span></div>
            </div>
        </div>
    </div>

    <!-- Live Routing Simulator & Rules Grid -->
    <div style="display:grid; grid-template-columns:1.5fr 1fr; gap:20px; margin-bottom:20px;">
        <!-- Left: Routing Rules Table -->
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
                <h3 style="font-size:15px; font-weight:700; margin:0; color:#0f172a;">Configured Business Rules</h3>
                <div style="display:flex; gap:8px;">
                    <select wire:model.live="businessFilter" style="border:1px solid #cbd5e1; border-radius:8px; padding:6px 10px; font-size:12px; background:white;">
                        <option value="all">All Businesses</option>
                        @foreach($businesses as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; text-align:left; font-size:12.5px;">
                    <thead>
                        <tr style="border-bottom:1px solid #e2e8f0; background:#f8fafc; color:#64748b;">
                            <th style="padding:10px 12px; font-weight:600;">RULE NAME</th>
                            <th style="padding:10px 12px; font-weight:600;">TARGET BUSINESS</th>
                            <th style="padding:10px 12px; font-weight:600;">KEYWORDS</th>
                            <th style="padding:10px 12px; font-weight:600;">MIN CONF</th>
                            <th style="padding:10px 12px; font-weight:600; text-align:right;">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rules as $rule)
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:12px;">
                                    <a href="{{ route('admin.routing.show', $rule) }}" style="font-weight:700; color:#0f172a; text-decoration:none;">
                                        {{ $rule->name }}
                                    </a>
                                    <div style="font-size:11px; color:#64748b;">Priority: {{ ucfirst($rule->priority) }}</div>
                                </td>
                                <td style="padding:12px;">
                                    <span style="font-weight:600; color:#334155;">{{ $rule->business->name ?? 'Unassigned' }}</span>
                                </td>
                                <td style="padding:12px;">
                                    <div style="display:flex; gap:3px; flex-wrap:wrap; max-width:200px;">
                                        @foreach(array_slice($rule->keywords, 0, 4) as $kw)
                                            <span style="background:#edf3ff; color:#3568ca; padding:1px 5px; border-radius:3px; font-size:10.5px; font-weight:600;">
                                                {{ $kw }}
                                            </span>
                                        @endforeach
                                        @if(count($rule->keywords) > 4)
                                            <span style="color:#64748b; font-size:10px;">+{{ count($rule->keywords) - 4 }} more</span>
                                        @endif
                                    </div>
                                </td>
                                <td style="padding:12px; font-family:monospace; font-weight:600;">
                                    {{ $rule->min_confidence }}
                                </td>
                                <td style="padding:12px; text-align:right;">
                                    <div style="display:inline-flex; gap:6px;">
                                        <button
                                            wire:click="toggleRule({{ $rule->id }})"
                                            style="padding:4px 8px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:5px; font-size:11px; cursor:pointer;"
                                        >
                                            {{ $rule->is_active ? 'Pause' : 'Activate' }}
                                        </button>
                                        <a href="{{ route('admin.routing.edit', $rule) }}" style="padding:4px 8px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:5px; font-size:11px; text-decoration:none;">
                                            Edit
                                        </a>
                                        <button
                                            wire:confirm="Delete this routing rule?"
                                            wire:click="deleteRule({{ $rule->id }})"
                                            style="padding:4px 8px; border:1px solid #fee2e2; background:#fef2f2; color:#dc2626; border-radius:5px; font-size:11px; cursor:pointer;"
                                        >
                                            ✕
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center; padding:28px; color:#64748b;">
                                    No routing rules configured.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top:12px;">
                {{ $rules->links() }}
            </div>
        </div>

        <!-- Right: Real-time Routing Simulator -->
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03); display:flex; flex-direction:column; justify-content:space-between;">
            <div>
                <h3 style="font-size:15px; font-weight:700; margin:0 0 6px; color:#0f172a;">Live Routing Simulator</h3>
                <p style="color:#64748b; font-size:12px; margin:0 0 12px;">
                    Test how our engine routes incoming messages and detects intent ambiguity.
                </p>

                <textarea
                    wire:model="testMessage"
                    rows="3"
                    placeholder="Enter sample enquiry (e.g. Need 500 kg garlic wholesale price OR Need business website with portfolio)..."
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:12.5px; margin-bottom:10px;"
                ></textarea>

                <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:12px;">
                    <button
                        type="button"
                        wire:click="setSampleTest('I need a responsive business website for my company')"
                        style="background:#f1f5f9; border:1px solid #e2e8f0; border-radius:6px; padding:4px 8px; font-size:11px; cursor:pointer; color:#334155;"
                    >
                        Sample: Website
                    </button>
                    <button
                        type="button"
                        wire:click="setSampleTest('Need 500 kg garlic wholesale price for Delhi delivery')"
                        style="background:#f1f5f9; border:1px solid #e2e8f0; border-radius:6px; padding:4px 8px; font-size:11px; cursor:pointer; color:#334155;"
                    >
                        Sample: Garlic
                    </button>
                    <button
                        type="button"
                        wire:click="setSampleTest('Tell me about your services and pricing details')"
                        style="background:#f1f5f9; border:1px solid #e2e8f0; border-radius:6px; padding:4px 8px; font-size:11px; cursor:pointer; color:#334155;"
                    >
                        Sample: Ambiguous
                    </button>
                </div>

                <button
                    wire:click="testRoute"
                    style="width:100%; background:#3978f6; color:white; border:0; border-radius:8px; padding:9px; font-size:13px; font-weight:700; cursor:pointer;"
                >
                    Evaluate Message Route
                </button>

                @if($testResult)
                    <div style="margin-top:14px; border:1px solid {{ $testResult['status'] === 'routed' ? '#bbf7d0' : '#fed7aa' }}; background:{{ $testResult['status'] === 'routed' ? '#f0fdf4' : '#fffbeb' }}; border-radius:8px; padding:12px; font-size:12.5px;">
                        @if($testResult['status'] === 'routed')
                            <div style="color:#15803d; font-weight:800; font-size:13px; margin-bottom:4px;">
                                ✓ Confident Route: {{ $testResult['business_name'] }}
                            </div>
                            <div style="color:#166534; font-size:11.5px; line-height:1.5;">
                                <div>Confidence: <b>{{ round($testResult['confidence'] * 100) }}%</b></div>
                                <div>Matched Keywords: <b>{{ implode(', ', $testResult['keywords']) }}</b></div>
                                <div>Explanation: {{ $testResult['reason'] }}</div>
                            </div>
                        @else
                            <div style="color:#b45309; font-weight:800; font-size:13px; margin-bottom:4px;">
                                ⚠ {{ $testResult['status'] === 'ambiguous' ? 'Ambiguous Match — Unassigned Review' : 'No Clear Match — Unassigned Review' }}
                            </div>
                            <div style="color:#92400e; font-size:11.5px; line-height:1.5;">
                                <div>Confidence: <b>{{ round($testResult['confidence'] * 100) }}%</b></div>
                                <div>Safeguard: <b>Will NOT auto-reply with guessed pricing or wrong product</b>. Enters Unassigned Review.</div>
                                <div>Reason: {{ $testResult['reason'] }}</div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div style="margin-top:16px; border-top:1px solid #f1f5f9; padding-top:10px; font-size:11px; color:#64748b; line-height:1.5;">
                🛡️ <b>Business Safety Protocol</b>: Ambiguous or low-confidence matches never trigger AI auto-replies or outbound calls. They are flagged for human operator review.
            </div>
        </div>
    </div>

    <!-- Recent Real Routing Decisions Log -->
    <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
        <h3 style="font-size:15px; font-weight:700; margin:0 0 14px; color:#0f172a;">Recent Routing Decisions Log (from MySQL)</h3>
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; text-align:left; font-size:12px;">
                <thead>
                    <tr style="border-bottom:1px solid #e2e8f0; background:#f8fafc; color:#64748b;">
                        <th style="padding:8px 10px;">TIMESTAMP</th>
                        <th style="padding:8px 10px;">INCOMING MESSAGE</th>
                        <th style="padding:8px 10px;">ROUTED BUSINESS</th>
                        <th style="padding:8px 10px;">CONFIDENCE</th>
                        <th style="padding:8px 10px;">STATUS</th>
                        <th style="padding:8px 10px;">REASON</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLogs as $log)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:10px; color:#64748b; white-space:nowrap;">{{ $log->created_at->diffForHumans() }}</td>
                            <td style="padding:10px; font-weight:600; color:#1e293b; max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                "{{ $log->incoming_text }}"
                            </td>
                            <td style="padding:10px;">
                                @if($log->matchedBusiness)
                                    <span style="font-weight:700; color:#0f172a;">{{ $log->matchedBusiness->name }}</span>
                                @else
                                    <span style="color:#94a3b8; font-style:italic;">Unassigned</span>
                                @endif
                            </td>
                            <td style="padding:10px; font-family:monospace; font-weight:700;">
                                {{ round($log->confidence * 100) }}%
                            </td>
                            <td style="padding:10px;">
                                @if($log->status === 'routed')
                                    <span style="background:#ecfdf5; color:#16a34a; padding:2px 7px; border-radius:12px; font-size:10.5px; font-weight:700;">Routed</span>
                                @else
                                    <span style="background:#fffbeb; color:#b45309; padding:2px 7px; border-radius:12px; font-size:10.5px; font-weight:700;">{{ ucfirst(str_replace('_', ' ', $log->status)) }}</span>
                                @endif
                            </td>
                            <td style="padding:10px; color:#64748b; font-size:11.5px;">{{ $log->reason }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:20px; color:#94a3b8;">
                                No routing decisions logged yet. Use the simulator above to generate your first live routing test.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div style="max-width:800px; margin:0 auto;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
        <div>
            <a href="{{ route('admin.routing.index') }}" style="color:#3978f6; font-size:13px; text-decoration:none; font-weight:600;">
                ← Back to Business Routing
            </a>
            <h1 style="font-size:24px; font-weight:800; margin:6px 0 4px; color:#0f172a;">{{ $rule->name }}</h1>
            <p style="margin:0; color:#64748b; font-size:13.5px;">
                Target Business: <b>{{ $rule->business->name }}</b> ({{ $rule->business->code ?? 'N/A' }})
            </p>
        </div>
        <div style="display:flex; gap:10px;">
            <button
                wire:click="toggle"
                style="padding:8px 14px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;"
            >
                {{ $rule->is_active ? 'Pause Rule' : 'Activate Rule' }}
            </button>
            <a href="{{ route('admin.routing.edit', $rule) }}" style="padding:8px 16px; background:#3978f6; color:white; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none;">
                Edit Rule
            </a>
        </div>
    </div>

    <!-- Rule Configuration Summary -->
    <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03); margin-bottom:20px;">
        <h3 style="font-size:15px; font-weight:700; margin:0 0 12px; color:#0f172a;">Rule Specifications</h3>
        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:12px; margin-bottom:14px; font-size:12.5px;">
            <div>
                <div style="color:#64748b;">Priority</div>
                <div style="font-weight:700; color:#0f172a; margin-top:2px;">{{ ucfirst($rule->priority) }}</div>
            </div>
            <div>
                <div style="color:#64748b;">Min Confidence</div>
                <div style="font-weight:700; color:#0f172a; margin-top:2px;">{{ $rule->min_confidence }}</div>
            </div>
            <div>
                <div style="color:#64748b;">Active Status</div>
                <div style="margin-top:2px;">
                    <span style="background:{{ $rule->is_active ? '#ecfdf5' : '#fef2f2' }}; color:{{ $rule->is_active ? '#16a34a' : '#dc2626' }}; padding:2px 8px; border-radius:20px; font-weight:700; font-size:11px;">
                        {{ $rule->is_active ? 'Active' : 'Paused' }}
                    </span>
                </div>
            </div>
        </div>

        <div style="border-top:1px solid #f1f5f9; padding-top:12px;">
            <div style="font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">Configured Keywords:</div>
            <div style="display:flex; gap:6px; flex-wrap:wrap;">
                @foreach($rule->keywords as $kw)
                    <span style="background:#edf3ff; color:#3568ca; padding:4px 8px; border-radius:6px; font-size:12px; font-weight:600;">
                        {{ $kw }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Execution Logs under this rule's target business -->
    <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
        <h3 style="font-size:15px; font-weight:700; margin:0 0 12px; color:#0f172a;">Recent Executions Matched</h3>
        <table style="width:100%; border-collapse:collapse; text-align:left; font-size:12px;">
            <thead>
                <tr style="border-bottom:1px solid #e2e8f0; background:#f8fafc; color:#64748b;">
                    <th style="padding:8px 10px;">TIMESTAMP</th>
                    <th style="padding:8px 10px;">MESSAGE</th>
                    <th style="padding:8px 10px;">CONFIDENCE</th>
                    <th style="padding:8px 10px;">STATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:10px; color:#64748b;">{{ $log->created_at->diffForHumans() }}</td>
                        <td style="padding:10px; font-weight:600;">"{{ $log->incoming_text }}"</td>
                        <td style="padding:10px; font-family:monospace; font-weight:700;">{{ round($log->confidence * 100) }}%</td>
                        <td style="padding:10px;">
                            <span style="background:#ecfdf5; color:#16a34a; padding:2px 6px; border-radius:4px; font-size:10.5px; font-weight:700;">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding:20px; color:#94a3b8;">
                            No logged executions for this business yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

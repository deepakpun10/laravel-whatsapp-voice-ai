<div>
    <!-- Page Header & Action Bar -->
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:22px; flex-wrap:wrap; gap:14px;">
        <div>
            <h1 style="font-size:24px; font-weight:800; margin:0 0 6px; color:#0f172a;">Voice Calls Intelligence</h1>
            <p style="margin:0; color:#64748b; font-size:13.5px;">
                Manage customer phone conversations, view exact call timings & durations, read AI transcripts, and export data.
            </p>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <button
                type="button"
                wire:click="exportCsv"
                style="background:white; border:1px solid #cbd5e1; color:#0f172a; padding:9px 16px; border-radius:9px; font-size:13px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; box-shadow:0 1px 2px rgba(0,0,0,0.05);"
            >
                📥 Download Data (CSV)
            </button>
            <a
                href="{{ route('admin.calls.create') }}"
                style="background:#3978f6; color:white; padding:9px 16px; border-radius:9px; font-size:13px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:6px; box-shadow:0 1px 3px rgba(57,120,246,0.3);"
            >
                ＋ Initiate Voice Call
            </a>
        </div>
    </div>

    <!-- Telephony Metrics Row -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:14px; margin-bottom:20px;">
        <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:16px; box-shadow:0 4px 16px rgba(0,0,0,.03);">
            <div style="color:#64748b; font-size:11.5px; font-weight:700; text-transform:uppercase;">Unique Leads / Numbers</div>
            <div style="font-size:26px; font-weight:800; color:#0f172a; margin-top:4px;">{{ $uniqueNumbersCount }}</div>
            <div style="font-size:11px; color:#64748b; margin-top:2px;">Phone numbers dialled</div>
        </div>
        <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:16px; box-shadow:0 4px 16px rgba(0,0,0,.03);">
            <div style="color:#64748b; font-size:11.5px; font-weight:700; text-transform:uppercase;">Total Calls Attempted</div>
            <div style="font-size:26px; font-weight:800; color:#2563eb; margin-top:4px;">{{ $totalCalls }}</div>
            <div style="font-size:11px; color:#64748b; margin-top:2px;">Inbound & Outbound logs</div>
        </div>
        <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:16px; box-shadow:0 4px 16px rgba(0,0,0,.03);">
            <div style="color:#64748b; font-size:11.5px; font-weight:700; text-transform:uppercase;">Connected & Completed</div>
            <div style="font-size:26px; font-weight:800; color:#16a34a; margin-top:4px;">{{ $completedCalls }}</div>
            <div style="font-size:11px; color:#16a34a; margin-top:2px;">Customer conversations</div>
        </div>
        <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:16px; box-shadow:0 4px 16px rgba(0,0,0,.03);">
            <div style="color:#64748b; font-size:11.5px; font-weight:700; text-transform:uppercase;">High-Intent / Meetings</div>
            <div style="font-size:26px; font-weight:800; color:#7c3aed; margin-top:4px;">{{ $interestedCalls }}</div>
            <div style="font-size:11px; color:#7c3aed; margin-top:2px;">Interested / Callback leads</div>
        </div>
    </div>

    <!-- Filters & View Switcher Card -->
    <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:18px 20px; box-shadow:0 4px 20px rgba(0,0,0,.03); margin-bottom:20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:14px;">
            <!-- View Mode Switcher -->
            <div style="display:flex; background:#f1f5f9; padding:3px; border-radius:9px; border:1px solid #e2e8f0;">
                <button
                    type="button"
                    wire:click="$set('viewMode', 'grouped')"
                    style="border:none; padding:6px 14px; border-radius:7px; font-size:12.5px; font-weight:700; cursor:pointer; transition:all 0.15s; {{ $viewMode === 'grouped' ? 'background:white; color:#0f172a; box-shadow:0 2px 6px rgba(0,0,0,0.08);' : 'background:transparent; color:#64748b;' }}"
                >
                    📱 Group by Phone Number
                </button>
                <button
                    type="button"
                    wire:click="$set('viewMode', 'all')"
                    style="border:none; padding:6px 14px; border-radius:7px; font-size:12.5px; font-weight:700; cursor:pointer; transition:all 0.15s; {{ $viewMode === 'all' ? 'background:white; color:#0f172a; box-shadow:0 2px 6px rgba(0,0,0,0.08);' : 'background:transparent; color:#64748b;' }}"
                >
                    📋 All Call Logs (Flat List)
                </button>
            </div>

            <!-- Date Range Quick Presets -->
            <div style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
                <span style="font-size:12px; font-weight:700; color:#64748b; margin-right:4px;">Date:</span>
                @foreach([
                    'all' => 'All Time',
                    'today' => 'Today',
                    '7_days' => 'Last 7 Days',
                    '30_days' => 'Last 30 Days'
                ] as $k => $label)
                    <button
                        type="button"
                        wire:click="setPreset('{{ $k }}')"
                        style="border:1px solid {{ $datePreset === $k ? '#3978f6' : '#e2e8f0' }}; background:{{ $datePreset === $k ? '#eff6ff' : 'white' }}; color:{{ $datePreset === $k ? '#1d4ed8' : '#475569' }}; padding:5px 11px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Filter Controls Row -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(160px, 1fr)); gap:10px; align-items:center;">
            <!-- Search -->
            <div style="position:relative;">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="🔍 Search phone number or customer name..."
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:8px 12px; font-size:12.5px; box-sizing:border-box;"
                />
            </div>

            <!-- Business Filter -->
            <select wire:model.live="businessFilter" style="border:1px solid #cbd5e1; border-radius:8px; padding:8px 10px; font-size:12.5px; background:white;">
                <option value="all">All Businesses</option>
                @foreach($businesses as $b)
                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                @endforeach
            </select>

            <!-- Outcome Filter -->
            <select wire:model.live="outcomeFilter" style="border:1px solid #cbd5e1; border-radius:8px; padding:8px 10px; font-size:12.5px; background:white;">
                <option value="all">All Outcomes</option>
                <option value="interested">✓ Interested Customer</option>
                <option value="meeting_scheduled">📅 Meeting Scheduled</option>
                <option value="callback_requested">◷ Callback Requested</option>
                <option value="human_handover">👤 Human Handover</option>
                <option value="completed">Completed / Neutral</option>
                <option value="ringing">Ringing / Active</option>
                <option value="failed">Failed / No Answer</option>
            </select>

            <!-- From Date -->
            <input
                type="date"
                wire:model.live="fromDate"
                title="From Date"
                style="border:1px solid #cbd5e1; border-radius:8px; padding:7px 10px; font-size:12px; background:white; width:100%; box-sizing:border-box;"
            />

            <!-- To Date -->
            <input
                type="date"
                wire:model.live="toDate"
                title="To Date"
                style="border:1px solid #cbd5e1; border-radius:8px; padding:7px 10px; font-size:12px; background:white; width:100%; box-sizing:border-box;"
            />

            <!-- Reset -->
            <button
                type="button"
                wire:click="clearFilters"
                style="background:#f8fafc; border:1px solid #cbd5e1; color:#64748b; padding:8px 12px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer;"
                title="Clear all filters"
            >
                Reset
            </button>
        </div>
    </div>

    <!-- Selection Action Bar (when checkboxes are checked) -->
    @if(count($selectedNumbers) > 0)
        <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:12px 18px; margin-bottom:18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div style="font-size:13px; font-weight:700; color:#1e40af;">
                ✓ {{ count($selectedNumbers) }} {{ $viewMode === 'grouped' ? 'phone number(s)' : 'call record(s)' }} selected
            </div>
            <div style="display:flex; gap:8px;">
                <button
                    type="button"
                    wire:click="exportCsv"
                    style="background:#1d4ed8; color:white; border:none; padding:7px 14px; border-radius:7px; font-size:12px; font-weight:700; cursor:pointer;"
                >
                    📥 Download Selected (CSV)
                </button>
                <button
                    type="button"
                    wire:click="$set('selectedNumbers', [])"
                    style="background:white; border:1px solid #cbd5e1; color:#475569; padding:7px 12px; border-radius:7px; font-size:12px; font-weight:600; cursor:pointer;"
                >
                    Deselect All
                </button>
            </div>
        </div>
    @endif

    <!-- VIEW 1: GROUPED BY PHONE NUMBER (Clean & Structured) -->
    @if($viewMode === 'grouped')
        <div style="display:flex; flex-direction:column; gap:14px;">
            @forelse($groupedRecords as $record)
                @php
                    $isExpanded = ($expandedNumber === $record['phone_number']);
                    $latestCall = $record['latest_call'];
                    $timeStr = $latestCall->started_at 
                        ? $latestCall->started_at->timezone('Asia/Kolkata')->format('d M Y, h:i A')
                        : $latestCall->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A');

                    $outcome = $record['latest_outcome'];
                    $outcomeColors = [
                        'interested' => ['bg' => '#ecfdf5', 'text' => '#15803d', 'border' => '#bbf7d0'],
                        'meeting_scheduled' => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'border' => '#bfdbfe'],
                        'callback_requested' => ['bg' => '#fefce8', 'text' => '#a16207', 'border' => '#fef08a'],
                        'human_handover' => ['bg' => '#fff7ed', 'text' => '#c2410c', 'border' => '#fed7aa'],
                        'completed' => ['bg' => '#f8fafc', 'text' => '#334155', 'border' => '#e2e8f0'],
                        'ringing' => ['bg' => '#fffbeb', 'text' => '#b45309', 'border' => '#fde68a'],
                        'failed' => ['bg' => '#fef2f2', 'text' => '#b91c1c', 'border' => '#fecaca'],
                    ];
                    $st = $outcomeColors[$outcome] ?? ['bg' => '#f8fafc', 'text' => '#475569', 'border' => '#e2e8f0'];
                @endphp

                <div wire:key="call-group-{{ md5($record['phone_number']) }}" style="background:white; border:1px solid {{ $isExpanded ? '#3b82f6' : '#e2e8f0' }}; border-radius:14px; box-shadow:0 4px 16px rgba(0,0,0,.03); overflow:hidden; transition:border-color 0.15s;">
                    <!-- Card Main Summary Header -->
                    <div style="padding:16px 20px; display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:14px; align-items:center;">
                        <!-- Select Checkbox -->
                        <div>
                            <input
                                type="checkbox"
                                wire:model.live="selectedNumbers"
                                value="{{ $record['phone_number'] }}"
                                style="width:16px; height:16px; cursor:pointer;"
                            />
                        </div>

                        <!-- Phone & Customer -->
                        <div>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span style="font-size:15px; font-weight:800; color:#0f172a; font-family:monospace;">
                                    {{ $record['phone_number'] }}
                                </span>
                                @if($record['calls_count'] > 1)
                                    <span style="background:#f1f5f9; color:#475569; font-size:11px; font-weight:700; padding:2px 8px; border-radius:10px;">
                                        {{ $record['calls_count'] }} Calls
                                    </span>
                                @endif
                            </div>
                            <div style="font-size:12px; color:#64748b; margin-top:2px;">
                                Lead: <b style="color:#1e293b;">{{ $record['contact']?->name ?? 'External Lead' }}</b>
                                @if($record['business'])
                                    · <span style="color:#3b82f6; font-weight:600;">{{ $record['business']->name }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Timing (Date & Time of Latest Call) -->
                        <div>
                            <div style="font-size:11px; color:#64748b; text-transform:uppercase; font-weight:700;">Latest Call Time</div>
                            <div style="font-size:12.5px; font-weight:700; color:#0f172a; margin-top:2px;">
                                📅 {{ $timeStr }}
                            </div>
                        </div>

                        <!-- Total Duration -->
                        <div>
                            <div style="font-size:11px; color:#64748b; text-transform:uppercase; font-weight:700;">Duration</div>
                            <div style="font-size:13px; font-weight:700; color:#0f172a; font-family:monospace; margin-top:2px;">
                                ⏱️ {{ gmdate('i:s', $record['total_duration']) }}
                                @if($record['total_duration'] > 60)
                                    <span style="font-size:11px; color:#64748b; font-weight:500;">({{ floor($record['total_duration']/60) }}m {{ $record['total_duration']%60 }}s)</span>
                                @endif
                            </div>
                        </div>

                        <!-- Outcome Badge -->
                        <div>
                            <div style="font-size:11px; color:#64748b; text-transform:uppercase; font-weight:700; margin-bottom:3px;">Call Intent</div>
                            <span style="background:{{ $st['bg'] }}; color:{{ $st['text'] }}; border:1px solid {{ $st['border'] }}; font-weight:700; font-size:11.5px; padding:3px 10px; border-radius:12px; text-transform:capitalize; display:inline-block;">
                                {{ str_replace('_', ' ', $outcome) }}
                            </span>
                        </div>

                        <!-- AI Summary Preview -->
                        <div style="max-width:240px;">
                            <div style="font-size:11px; color:#64748b; text-transform:uppercase; font-weight:700;">AI Key Takeaway</div>
                            <div style="font-size:12px; color:#1e293b; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-top:2px;" title="{{ $record['latest_summary'] }}">
                                {{ $record['latest_summary'] ?: 'Call connected and logged.' }}
                            </div>
                        </div>

                        <!-- Expand / History Button -->
                        <div style="text-align:right;">
                            <button
                                type="button"
                                wire:click="toggleExpand('{{ $record['phone_number'] }}')"
                                style="background:{{ $isExpanded ? '#eff6ff' : '#f8fafc' }}; border:1px solid {{ $isExpanded ? '#bfdbfe' : '#cbd5e1' }}; color:{{ $isExpanded ? '#1d4ed8' : '#334155' }}; padding:7px 14px; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px;"
                            >
                                {{ $isExpanded ? 'Hide History ▲' : "History ({$record['calls_count']}) ▼" }}
                            </button>
                        </div>
                    </div>

                    <!-- EXPANDED ACCORDION: Complete Chronological Calls & Transcripts for this Number -->
                    @if($isExpanded)
                        <div style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:18px 24px;">
                            <div style="font-size:13px; font-weight:800; color:#0f172a; margin-bottom:14px; display:flex; justify-content:space-between; align-items:center;">
                                <span>📜 Complete Call History for {{ $record['phone_number'] }}</span>
                                <span style="font-size:12px; color:#64748b; font-weight:600;">Sorted newest to oldest</span>
                            </div>

                            <div style="display:flex; flex-direction:column; gap:12px;">
                                @foreach($record['calls'] as $idx => $callItem)
                                    @php
                                        $cTime = $callItem->started_at 
                                            ? $callItem->started_at->timezone('Asia/Kolkata')->format('d M Y, h:i:s A')
                                            : $callItem->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i:s A');
                                    @endphp
                                    <div style="background:white; border:1px solid #e2e8f0; border-radius:10px; padding:16px; box-shadow:0 2px 8px rgba(0,0,0,.02);">
                                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px; flex-wrap:wrap; gap:8px;">
                                            <div>
                                                <div style="font-weight:700; font-size:13px; color:#0f172a;">
                                                    Call #{{ $callItem->id }} (Attempt {{ count($record['calls']) - $idx }})
                                                    <span style="margin-left:8px; font-weight:500; color:#64748b; font-size:12px;">
                                                        📅 {{ $cTime }}
                                                    </span>
                                                </div>
                                                <div style="font-size:11.5px; color:#64748b; margin-top:2px;">
                                                    Line: {{ $callItem->from_number ?: 'Default Sarvam Line' }} · Agent: {{ $callItem->aiAgent?->name ?? 'Pundeer Voice Agent' }}
                                                </div>
                                            </div>

                                            <div style="display:flex; gap:10px; align-items:center;">
                                                <span style="font-family:monospace; font-size:12px; font-weight:700; background:#f1f5f9; padding:3px 8px; border-radius:6px; color:#0f172a;">
                                                    ⏱️ {{ gmdate('i:s', $callItem->duration_seconds) }}
                                                </span>
                                                <span style="background:{{ $st['bg'] }}; color:{{ $st['text'] }}; font-weight:700; font-size:11px; padding:3px 8px; border-radius:10px; text-transform:capitalize;">
                                                    {{ str_replace('_', ' ', $callItem->outcome ?: $callItem->status) }}
                                                </span>
                                                <a
                                                    href="{{ route('admin.calls.show', $callItem) }}"
                                                    style="background:#3978f6; color:white; padding:4px 10px; border-radius:6px; font-size:11.5px; font-weight:700; text-decoration:none;"
                                                >
                                                    Full Page View →
                                                </a>
                                            </div>
                                        </div>

                                        <!-- AI Summary snippet -->
                                        @if($callItem->summary)
                                            <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:8px 12px; margin-bottom:10px; font-size:12px; color:#1e3a8a;">
                                                <b>🧠 AI Summary:</b> {{ $callItem->summary }}
                                            </div>
                                        @endif

                                        <!-- Customer Requirements snippet -->
                                        @if(!empty($callItem->extracted_facts))
                                            <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:8px 12px; margin-bottom:10px; font-size:12px; color:#14532d; display:flex; gap:14px; flex-wrap:wrap;">
                                                <b>📊 Extracted Info:</b>
                                                @foreach($callItem->extracted_facts as $k => $v)
                                                    <span>{{ ucwords(str_replace('_', ' ', $k)) }}: <b>{{ is_array($v) ? json_encode($v) : $v }}</b></span>
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Transcript snippet -->
                                        @if($callItem->transcript)
                                            <details style="margin-top:8px;">
                                                <summary style="font-size:12px; font-weight:700; color:#3b82f6; cursor:pointer;">
                                                    📄 View Word-to-Word Conversation Transcript
                                                </summary>
                                                <div style="margin-top:6px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:10px; font-size:12px; line-height:1.5; color:#334155; white-space:pre-wrap; max-height:180px; overflow-y:auto;">
{{ $callItem->transcript }}
                                                </div>
                                            </details>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:40px; text-align:center;">
                    <div style="font-size:36px; margin-bottom:10px;">📞</div>
                    <h3 style="font-size:16px; font-weight:700; margin:0 0 6px; color:#0f172a;">No voice calls found</h3>
                    <p style="color:#64748b; font-size:13px; margin:0 0 16px;">There are no voice call records matching your active filters or date range.</p>
                    <button
                        type="button"
                        wire:click="clearFilters"
                        style="background:#f1f5f9; border:1px solid #cbd5e1; color:#334155; padding:8px 16px; border-radius:8px; font-size:12.5px; font-weight:700; cursor:pointer;"
                    >
                        Clear Active Filters
                    </button>
                </div>
            @endforelse
        </div>

    <!-- VIEW 2: ALL CALL LOGS (Flat List View) -->
    @else
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 8px 24px rgba(0,0,0,.04);">
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; text-align:left; font-size:12.5px;">
                    <thead>
                        <tr style="border-bottom:1px solid #e2e8f0; background:#f8fafc; color:#64748b;">
                            <th style="padding:10px 12px; width:30px;">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectAll"
                                    style="width:15px; height:15px; cursor:pointer;"
                                />
                            </th>
                            <th style="padding:10px 12px; font-weight:700;">CALL TIME & DATE</th>
                            <th style="padding:10px 12px; font-weight:700;">CUSTOMER / NUMBER</th>
                            <th style="padding:10px 12px; font-weight:700;">BUSINESS</th>
                            <th style="padding:10px 12px; font-weight:700;">DURATION</th>
                            <th style="padding:10px 12px; font-weight:700;">OUTCOME / INTENT</th>
                            <th style="padding:10px 12px; font-weight:700;">AI SUMMARY</th>
                            <th style="padding:10px 12px; font-weight:700; text-align:right;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($calls as $c)
                            @php
                                $cTime = $c->started_at 
                                    ? $c->started_at->timezone('Asia/Kolkata')->format('d M Y, h:i A')
                                    : $c->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A');
                            @endphp
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:12px;">
                                    <input
                                        type="checkbox"
                                        wire:model.live="selectedNumbers"
                                        value="{{ $c->id }}"
                                        style="width:15px; height:15px; cursor:pointer;"
                                    />
                                </td>
                                <td style="padding:12px; font-size:12px; white-space:nowrap;">
                                    <b>{{ $cTime }}</b>
                                    <div style="font-size:11px; color:#64748b;">ID: #{{ $c->id }} ({{ ucfirst($c->direction) }})</div>
                                </td>
                                <td style="padding:12px;">
                                    <b style="color:#0f172a;">{{ $c->to_number }}</b>
                                    <div style="color:#64748b; font-size:11.5px;">{{ $c->contact->name ?? 'External Lead' }}</div>
                                </td>
                                <td style="padding:12px;">
                                    <span style="font-weight:600; color:#0f172a;">{{ $c->business->name ?? 'Unassigned' }}</span>
                                </td>
                                <td style="padding:12px; font-family:monospace; font-size:12px;">
                                    {{ gmdate('i:s', $c->duration_seconds) }}
                                    @if($c->duration_seconds > 0)
                                        <div style="font-size:10.5px; color:#16a34a; font-family:sans-serif;">({{ $c->duration_seconds }}s)</div>
                                    @endif
                                </td>
                                <td style="padding:12px;">
                                    <span style="background:#ecfdf5; color:#16a34a; font-weight:700; font-size:11px; padding:3px 8px; border-radius:12px; text-transform:capitalize;">
                                        {{ str_replace('_', ' ', $c->outcome ?: $c->status) }}
                                    </span>
                                </td>
                                <td style="padding:12px; max-width:240px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $c->summary }}">
                                    {{ $c->summary ?: '—' }}
                                </td>
                                <td style="padding:12px; text-align:right;">
                                    <a href="{{ route('admin.calls.show', $c) }}" style="padding:4px 10px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:5px; font-size:11.5px; font-weight:600; text-decoration:none;">
                                        Transcript →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center; padding:32px; color:#94a3b8;">
                                    No calls found matching active filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($calls->hasPages())
                <div style="margin-top:14px;">
                    {{ $calls->links() }}
                </div>
            @endif
        </div>
    @endif
</div>

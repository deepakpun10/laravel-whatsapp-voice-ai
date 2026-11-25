<div>
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:22px; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 style="font-size:24px; font-weight:800; margin:0 0 6px; color:#0f172a;">Follow-ups</h1>
            <p style="margin:0; color:#64748b; font-size:13.5px;">Callbacks and reminders strictly linked to the correct customer and business.</p>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1.5fr 1fr; gap:20px;">
        <!-- Left: Follow-ups Table -->
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
            <h3 style="font-size:16px; font-weight:700; margin:0 0 14px; color:#0f172a;">Scheduled Follow-ups</h3>
            <table style="width:100%; border-collapse:collapse; text-align:left; font-size:12.5px;">
                <thead>
                    <tr style="border-bottom:1px solid #e2e8f0; background:#f8fafc; color:#64748b;">
                        <th style="padding:10px;">CONTACT</th>
                        <th style="padding:10px;">BUSINESS</th>
                        <th style="padding:10px;">DUE</th>
                        <th style="padding:10px;">STATUS</th>
                        <th style="padding:10px; text-align:right;">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($followUps as $f)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:12px;">
                                <b>{{ $f->contact->name ?? 'Customer' }}</b>
                                <div style="font-size:11.5px; color:#64748b;">{{ $f->title }}</div>
                            </td>
                            <td style="padding:12px; font-weight:600; color:#334155;">
                                {{ $f->business->name }}
                            </td>
                            <td style="padding:12px; font-size:12px; color:#64748b;">
                                {{ $f->due_at->format('M d, h:i A') }}
                            </td>
                            <td style="padding:12px;">
                                <span style="font-size:11px; font-weight:700; padding:2px 7px; border-radius:10px; {{ $f->status === 'completed' ? 'background:#ecfdf5; color:#16a34a;' : 'background:#fffbeb; color:#b45309;' }}">
                                    {{ ucfirst($f->status) }}
                                </span>
                            </td>
                            <td style="padding:12px; text-align:right;">
                                @if($f->status === 'pending')
                                    <button
                                        wire:click="markCompleted({{ $f->id }})"
                                        style="padding:4px 8px; border:1px solid #86efac; background:#f0fdf4; color:#15803d; border-radius:5px; font-size:11px; font-weight:700; cursor:pointer;"
                                    >
                                        ✓ Complete
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:26px; color:#94a3b8;">
                                No follow-ups scheduled. Use the form on the right to schedule one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Right: Schedule Form -->
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03); height:fit-content;">
            <h3 style="font-size:16px; font-weight:700; margin:0 0 12px; color:#0f172a;">Schedule New Follow-up</h3>
            <form wire:submit="createFollowUp">
                <div style="margin-bottom:12px;">
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Contact *</label>
                    <select wire:model="contact_id" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:8px 10px; font-size:12.5px; background:white;">
                        <option value="">-- Choose Contact --</option>
                        @foreach($contacts as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone }})</option>
                        @endforeach
                    </select>
                    @error('contact_id') <span style="color:#dc2626; font-size:11px;">{{ $message }}</span> @enderror
                </div>

                <div style="margin-bottom:12px;">
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Business Workspace *</label>
                    <select wire:model="business_id" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:8px 10px; font-size:12.5px; background:white;">
                        @foreach($businesses as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom:12px;">
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Follow-up Title / Reason *</label>
                    <input wire:model="title" type="text" placeholder="e.g. Website quote discussion" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:8px 10px; font-size:12.5px;">
                    @error('title') <span style="color:#dc2626; font-size:11px;">{{ $message }}</span> @enderror
                </div>

                <div style="margin-bottom:12px;">
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Due Date & Time *</label>
                    <input wire:model="due_at" type="datetime-local" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:8px 10px; font-size:12.5px;">
                </div>

                <button type="submit" style="width:100%; background:#3978f6; color:white; border:0; border-radius:7px; padding:9px; font-size:13px; font-weight:700; cursor:pointer;">
                    Schedule Follow-up
                </button>
            </form>
        </div>
    </div>
</div>

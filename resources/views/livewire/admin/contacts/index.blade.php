<div>
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:22px; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 style="font-size:24px; font-weight:800; margin:0 0 6px; color:#0f172a;">Contacts & Leads</h1>
            <p style="margin:0; color:#64748b; font-size:13.5px;">Auto-detected from inbound WhatsApp enquiries, incoming voice calls, and manual entry.</p>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('admin.contacts.create') }}" style="background:#3978f6; color:white; padding:9px 16px; border-radius:9px; font-size:13px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                ＋ Add Lead
            </a>
        </div>
    </div>

    <!-- Filter and Search Toolbar -->
    <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 8px 24px rgba(0,0,0,.04);">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:16px; flex-wrap:wrap;">
            <div style="display:flex; gap:10px; flex:1; min-width:260px; flex-wrap:wrap;">
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search name, phone, or requirement..."
                    style="border:1px solid #cbd5e1; border-radius:8px; padding:8px 12px; font-size:13px; width:100%; max-width:320px;"
                >
                <select
                    wire:model.live="businessFilter"
                    style="border:1px solid #cbd5e1; border-radius:8px; padding:8px 12px; font-size:13px; background:white;"
                >
                    <option value="all">All Businesses</option>
                    @foreach($businesses as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                    <option value="unassigned">⚠️ Unassigned Review</option>
                </select>
                <select
                    wire:model.live="statusFilter"
                    style="border:1px solid #cbd5e1; border-radius:8px; padding:8px 12px; font-size:13px; background:white;"
                >
                    <option value="all">All Statuses</option>
                    <option value="new">New</option>
                    <option value="interested">Interested</option>
                    <option value="qualified">Qualified</option>
                    <option value="follow_up">Follow-up</option>
                    <option value="converted">Converted</option>
                    <option value="lost">Lost</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; text-align:left; font-size:13px;">
                <thead>
                    <tr style="border-bottom:1px solid #e2e8f0; background:#f8fafc; color:#64748b;">
                        <th style="padding:12px 14px; font-weight:600;">CONTACT</th>
                        <th style="padding:12px 14px; font-weight:600;">ASSIGNED BUSINESS</th>
                        <th style="padding:12px 14px; font-weight:600;">REQUIREMENT</th>
                        <th style="padding:12px 14px; font-weight:600;">SOURCE</th>
                        <th style="padding:12px 14px; font-weight:600;">STATUS</th>
                        <th style="padding:12px 14px; font-weight:600; text-align:right;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $contact)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:14px;">
                                <a href="{{ route('admin.contacts.show', $contact) }}" style="font-weight:700; color:#0f172a; text-decoration:none; font-size:14px;">
                                    {{ $contact->name }}
                                </a>
                                <div style="color:#64748b; font-size:12px; margin-top:2px;">{{ $contact->phone }}</div>
                            </td>
                            <td style="padding:14px;">
                                @if($contact->business)
                                    <span style="background:#edf3ff; color:#3568ca; padding:3px 8px; border-radius:4px; font-weight:600; font-size:11.5px;">
                                        {{ $contact->business->name }}
                                    </span>
                                @else
                                    <span style="background:#fffbeb; color:#b45309; padding:3px 8px; border-radius:4px; font-weight:700; font-size:11px;">
                                        ⚠️ Unassigned Review
                                    </span>
                                @endif
                            </td>
                            <td style="padding:14px; color:#475569;">
                                {{ $contact->requirement ?: 'Pending qualification' }}
                            </td>
                            <td style="padding:14px;">
                                <span style="text-transform:capitalize; color:#64748b; font-size:12px;">
                                    {{ $contact->source }}
                                </span>
                            </td>
                            <td style="padding:14px;">
                                @php
                                    $badgeStyle = match($contact->lead_status) {
                                        'qualified' => 'background:#ecfdf5; color:#16a34a;',
                                        'converted' => 'background:#f0fdf4; color:#15803d;',
                                        'interested' => 'background:#eff6ff; color:#2563eb;',
                                        'follow_up' => 'background:#fffbeb; color:#b45309;',
                                        'lost' => 'background:#fef2f2; color:#dc2626;',
                                        default => 'background:#f1f5f9; color:#475569;',
                                    };
                                @endphp
                                <span style="{{ $badgeStyle }} padding:3px 9px; border-radius:12px; font-size:11.5px; font-weight:700; text-transform:capitalize;">
                                    {{ str_replace('_', ' ', $contact->lead_status) }}
                                </span>
                            </td>
                            <td style="padding:14px; text-align:right;">
                                <div style="display:inline-flex; gap:8px;">
                                    <a href="{{ route('admin.contacts.show', $contact) }}" style="padding:5px 10px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:6px; font-size:12px; text-decoration:none; font-weight:600;">
                                        View
                                    </a>
                                    <a href="{{ route('admin.contacts.edit', $contact) }}" style="padding:5px 10px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:6px; font-size:12px; text-decoration:none; font-weight:600;">
                                        Edit
                                    </a>
                                    <button
                                        type="button"
                                        wire:confirm="Delete contact {{ $contact->name }}?"
                                        wire:click="deleteContact({{ $contact->id }})"
                                        style="padding:5px 10px; border:1px solid #fee2e2; background:#fef2f2; color:#dc2626; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:36px; color:#64748b;">
                                No contacts found. Contacts automatically create when an enquiry is received.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:16px;">
            {{ $contacts->links() }}
        </div>
    </div>
</div>

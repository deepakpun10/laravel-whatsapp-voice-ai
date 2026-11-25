<div>
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:22px; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 style="font-size:24px; font-weight:800; margin:0 0 6px; color:#0f172a;">Businesses (Tenants)</h1>
            <p style="margin:0; color:#64748b; font-size:13.5px;">Manage independent tenant workspaces, isolated RAG scopes, and business credentials.</p>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('admin.businesses.create') }}" style="background:#3978f6; color:white; padding:9px 16px; border-radius:9px; font-size:13px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                ＋ Add Business
            </a>
        </div>
    </div>

    <!-- Stats row -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-bottom:20px;">
        <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:16px; box-shadow:0 4px 16px rgba(0,0,0,.03);">
            <div style="color:#64748b; font-size:12px; font-weight:600;">TOTAL WORKSPACES</div>
            <div style="font-size:26px; font-weight:800; color:#0f172a; margin-top:4px;">{{ $totalCount }}</div>
        </div>
        <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:16px; box-shadow:0 4px 16px rgba(0,0,0,.03);">
            <div style="color:#64748b; font-size:12px; font-weight:600;">ACTIVE BUSINESSES</div>
            <div style="font-size:26px; font-weight:800; color:#16a34a; margin-top:4px;">{{ $activeCount }}</div>
        </div>
    </div>

    <!-- Filter and Search Toolbar -->
    <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 8px 24px rgba(0,0,0,.04);">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:16px; flex-wrap:wrap;">
            <div style="display:flex; gap:10px; flex:1; min-width:260px;">
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search name, code, or category..."
                    style="border:1px solid #cbd5e1; border-radius:8px; padding:8px 12px; font-size:13px; width:100%; max-width:340px;"
                >
                <select
                    wire:model.live="statusFilter"
                    style="border:1px solid #cbd5e1; border-radius:8px; padding:8px 12px; font-size:13px; background:white;"
                >
                    <option value="all">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; text-align:left; font-size:13px;">
                <thead>
                    <tr style="border-bottom:1px solid #e2e8f0; background:#f8fafc; color:#64748b;">
                        <th style="padding:12px 14px; font-weight:600;">BUSINESS NAME</th>
                        <th style="padding:12px 14px; font-weight:600;">CODE</th>
                        <th style="padding:12px 14px; font-weight:600;">CATEGORY</th>
                        <th style="padding:12px 14px; font-weight:600;">STATUS</th>
                        <th style="padding:12px 14px; font-weight:600;">SCOPED ASSETS</th>
                        <th style="padding:12px 14px; font-weight:600; text-align:right;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($businesses as $business)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:14px;">
                                <a href="{{ route('admin.businesses.show', $business) }}" style="font-weight:700; color:#0f172a; text-decoration:none; font-size:14px;">
                                    {{ $business->name }}
                                </a>
                                <div style="color:#64748b; font-size:11.5px; margin-top:2px;">{{ Str::limit($business->description, 50) }}</div>
                            </td>
                            <td style="padding:14px;">
                                <span style="background:#f1f5f9; color:#475569; padding:3px 8px; border-radius:6px; font-family:monospace; font-weight:600; font-size:11.5px;">
                                    {{ $business->code ?? 'N/A' }}
                                </span>
                            </td>
                            <td style="padding:14px; color:#475569;">
                                {{ $business->category ?? 'General' }}
                            </td>
                            <td style="padding:14px;">
                                @if($business->status === 'active')
                                    <span style="background:#ecfdf5; color:#16a34a; padding:4px 9px; border-radius:20px; font-size:11.5px; font-weight:700;">● Active</span>
                                @else
                                    <span style="background:#fef2f2; color:#dc2626; padding:4px 9px; border-radius:20px; font-size:11.5px; font-weight:700;">● Inactive</span>
                                @endif
                            </td>
                            <td style="padding:14px; font-size:12px; color:#64748b;">
                                <div><b>{{ $business->contacts_count }}</b> contacts · <b>{{ $business->conversations_count }}</b> chats</div>
                                <div style="font-size:11px;"><b>{{ $business->ai_agents_count }}</b> agents · <b>{{ $business->knowledge_bases_count }}</b> KBs</div>
                            </td>
                            <td style="padding:14px; text-align:right;">
                                <div style="display:inline-flex; gap:8px;">
                                    <a href="{{ route('admin.businesses.show', $business) }}" style="padding:5px 10px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:6px; font-size:12px; text-decoration:none; font-weight:600;">
                                        View
                                    </a>
                                    <a href="{{ route('admin.businesses.edit', $business) }}" style="padding:5px 10px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:6px; font-size:12px; text-decoration:none; font-weight:600;">
                                        Edit
                                    </a>
                                    <button
                                        type="button"
                                        wire:confirm="Are you sure you want to delete {{ $business->name }}? This will soft delete the workspace."
                                        wire:click="deleteBusiness({{ $business->id }})"
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
                                No business workspaces found matching your search.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:16px;">
            {{ $businesses->links() }}
        </div>
    </div>
</div>

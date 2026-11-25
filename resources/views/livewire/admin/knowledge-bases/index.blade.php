<div>
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:22px; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 style="font-size:24px; font-weight:800; margin:0 0 6px; color:#0f172a;">Knowledge Bases</h1>
            <p style="margin:0; color:#64748b; font-size:13.5px;">Strictly separated RAG document collections for each business. Shared WhatsApp numbers never mix knowledge.</p>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('admin.knowledge-bases.create') }}" style="background:#3978f6; color:white; padding:9px 16px; border-radius:9px; font-size:13px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                ＋ New Knowledge Base
            </a>
        </div>
    </div>

    <!-- Isolation Notice Banner matching prototype -->
    <div style="background:#ecfdf5; border:1px solid #a7f3d0; border-radius:10px; padding:12px 16px; color:#065f46; font-size:12.5px; line-height:1.5; margin-bottom:22px;">
        🛡️ <b>RAG Multi-Tenant Isolation Rule</b>: Each knowledge base belongs strictly to one business. Vector search enforces mandatory tenant and business ID filters server-side before similarity scoring.
    </div>

    <!-- Knowledge Bases Cards Grid -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:18px; margin-bottom:22px;">
        @forelse($knowledgeBases as $kb)
            <div style="background:white; border:1px solid {{ in_array($kb->status, ['inactive', 'disabled']) ? '#fecaca' : '#e2e8f0' }}; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03); display:flex; flex-direction:column; justify-content:space-between;">
                <div>
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
                        <div>
                            <h3 style="font-size:16px; font-weight:700; margin:0; color:#0f172a;">{{ $kb->name }}</h3>
                            <div style="font-size:12px; color:#3568ca; font-weight:600; margin-top:2px;">
                                🏢 {{ $kb->business->name }}
                            </div>
                        </div>
                        <div>
                            @if(in_array($kb->status, ['inactive', 'disabled']))
                                <button
                                    wire:click="toggleStatus({{ $kb->id }})"
                                    title="Click to Activate"
                                    style="font-size:11px; font-weight:700; padding:3px 8px; border-radius:12px; background:#fee2e2; color:#dc2626; border:1px solid #fca5a5; cursor:pointer;"
                                >
                                    ⏸️ Inactive (Off)
                                </button>
                            @else
                                <button
                                    wire:click="toggleStatus({{ $kb->id }})"
                                    title="Click to Deactivate"
                                    style="font-size:11px; font-weight:700; padding:3px 8px; border-radius:12px; background:#ecfdf5; color:#16a34a; border:1px solid #a7f3d0; cursor:pointer;"
                                >
                                    ● Active (On)
                                </button>
                            @endif
                        </div>
                    </div>

                    <p style="color:#64748b; font-size:12.5px; line-height:1.5; margin:0 0 14px;">
                        {{ $kb->description ?: 'Knowledge source collection for customer queries and RAG retrieval.' }}
                    </p>

                    <div style="background:#f8fafc; border-radius:8px; padding:10px; font-size:12px; color:#475569; margin-bottom:14px; display:flex; justify-content:space-between; align-items:center;">
                        <span>Indexed Documents: <b>{{ $kb->documents_count }}</b></span>
                        <span style="font-size:11px; color:{{ in_array($kb->status, ['inactive', 'disabled']) ? '#dc2626' : '#16a34a' }}; font-weight:600;">
                            {{ in_array($kb->status, ['inactive', 'disabled']) ? 'RAG Excluded' : 'RAG Enabled' }}
                        </span>
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #f1f5f9; padding-top:12px;">
                    <div style="display:flex; gap:6px;">
                        <a href="{{ route('admin.knowledge-bases.show', $kb) }}" style="padding:6px 12px; background:#edf3ff; color:#3568ca; border-radius:6px; font-size:12px; font-weight:700; text-decoration:none;">
                            Upload Files & Manage →
                        </a>
                        <a href="{{ route('admin.knowledge-bases.edit', $kb) }}" style="padding:6px 10px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:6px; font-size:12px; font-weight:600; text-decoration:none;">
                            Edit
                        </a>
                    </div>
                    <button
                        wire:confirm="Delete Knowledge Base {{ $kb->name }} and all indexed documents?"
                        wire:click="deleteKb({{ $kb->id }})"
                        style="padding:6px 8px; border:1px solid #fee2e2; background:#fef2f2; color:#dc2626; border-radius:6px; font-size:11.5px; cursor:pointer;"
                    >
                        ✕
                    </button>
                </div>
            </div>
        @empty
            <div style="grid-column:1/-1; background:white; border:1px solid #e2e8f0; border-radius:14px; padding:40px; text-align:center; color:#64748b;">
                No Knowledge Bases created yet. Create knowledge collections for Pundeer Technologies and Himachal Garlic.
            </div>
        @endforelse
    </div>

    <div>
        {{ $knowledgeBases->links() }}
    </div>
</div>

<div>
    <!-- Header with Breadcrumb and Active/Inactive Toggle -->
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
        <div>
            <a href="{{ route('admin.knowledge-bases.index') }}" style="color:#3978f6; font-size:13px; text-decoration:none; font-weight:600;">
                ← Back to Knowledge Bases
            </a>
            <div style="display:flex; align-items:center; gap:10px; margin-top:6px;">
                <h1 style="font-size:24px; font-weight:800; margin:0; color:#0f172a;">{{ $knowledgeBase->name }}</h1>
                @if(in_array($knowledgeBase->status, ['inactive', 'disabled']))
                    <span style="background:#fee2e2; color:#dc2626; font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px; border:1px solid #fecaca; display:inline-flex; align-items:center; gap:4px;">
                        ⏸️ Inactive (Band Hai / Disabled)
                    </span>
                @else
                    <span style="background:#ecfdf5; color:#16a34a; font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px; border:1px solid #a7f3d0; display:inline-flex; align-items:center; gap:4px;">
                        ● Active (Chalu Hai / RAG Enabled)
                    </span>
                @endif
            </div>
            <p style="margin:4px 0 0; color:#64748b; font-size:13.5px;">
                Scoped to: <b>{{ $knowledgeBase->business->name }}</b> · {{ $knowledgeBase->documents->count() }} documents indexed
            </p>
        </div>

        <!-- Quick Actions: Active/Inactive Toggle & Edit -->
        <div style="display:flex; align-items:center; gap:8px;">
            <button
                wire:click="toggleStatus"
                style="padding:8px 14px; border-radius:8px; font-size:12.5px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px; {{ in_array($knowledgeBase->status, ['inactive', 'disabled']) ? 'background:#16a34a; color:white; border:0;' : 'background:#fff; color:#dc2626; border:1px solid #fca5a5;' }}"
            >
                @if(in_array($knowledgeBase->status, ['inactive', 'disabled']))
                    ✓ Turn ON / Active Karein
                @else
                    ⏸️ Turn OFF / Inactive Karein
                @endif
            </button>
            <a
                href="{{ route('admin.knowledge-bases.edit', $knowledgeBase) }}"
                style="padding:8px 14px; background:white; border:1px solid #cbd5e1; color:#334155; border-radius:8px; font-size:12.5px; font-weight:600; text-decoration:none;"
            >
                Edit Details
            </a>
        </div>
    </div>

    @if(in_array($knowledgeBase->status, ['inactive', 'disabled']))
        <div style="background:#fff1f2; border:1px solid #fecdd3; border-radius:10px; padding:12px 16px; color:#be123c; font-size:12.5px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between;">
            <div>
                ⚠️ <b>Yeh Collection Abhi Inactive (Band) Hai:</b> Is collection ke kisi bhi document ka data AI bot customer chat ya RAG me use nahi karega.
            </div>
            <button wire:click="toggleStatus" style="background:#be123c; color:white; border:0; padding:4px 10px; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer;">
                Reactivate Now
            </button>
        </div>
    @endif

    <div style="display:grid; grid-template-columns:1.5fr 1fr; gap:20px;">
        <!-- Left Column: Ingest Documents & List -->
        <div style="display:flex; flex-direction:column; gap:20px;">
            
            <!-- Ingest Document Card with Tabs -->
            <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
                <!-- Tab Controls -->
                <div style="display:flex; gap:10px; border-bottom:1px solid #e2e8f0; padding-bottom:12px; margin-bottom:16px;">
                    <button
                        type="button"
                        wire:click="$set('activeInputMode', 'files')"
                        style="padding:6px 14px; border-radius:7px; font-size:12.5px; font-weight:700; border:0; cursor:pointer; {{ $activeInputMode === 'files' ? 'background:#3978f6; color:white;' : 'background:#f1f5f9; color:#475569;' }}"
                    >
                        📁 Upload Files (.md, .txt, .pdf, .json)
                    </button>
                    <button
                        type="button"
                        wire:click="$set('activeInputMode', 'text')"
                        style="padding:6px 14px; border-radius:7px; font-size:12.5px; font-weight:700; border:0; cursor:pointer; {{ $activeInputMode === 'text' ? 'background:#3978f6; color:white;' : 'background:#f1f5f9; color:#475569;' }}"
                    >
                        ✍️ Paste Raw Text / Markdown
                    </button>
                </div>

                <!-- TAB 1: Multiple File Upload Form -->
                @if($activeInputMode === 'files')
                    <div>
                        <div style="margin-bottom:12px;">
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:4px;">
                                Select Multiple Files (.md, .txt, .pdf, .json, .csv) *
                            </label>
                            <p style="font-size:11.5px; color:#64748b; margin:0 0 10px;">
                                Aap ek sath multiple markdown ya text files choose kar sakte hain. System sabhi files ko automatically parse aur vector chunking karke index karega.
                            </p>
                            
                            <div style="border:2px dashed #cbd5e1; border-radius:10px; padding:24px; text-align:center; background:#f8fafc; transition:border-color .2s;">
                                <input
                                    type="file"
                                    wire:model="uploadedFiles"
                                    multiple
                                    accept=".md,.markdown,.txt,.text,.pdf,.json,.csv"
                                    id="file_input"
                                    style="display:none;"
                                >
                                <label for="file_input" style="cursor:pointer; display:inline-block;">
                                    <div style="font-size:28px; margin-bottom:6px;">📄</div>
                                    <span style="display:inline-block; background:#3978f6; color:white; font-size:12px; font-weight:700; padding:8px 16px; border-radius:7px;">
                                        Choose Multiple Files
                                    </span>
                                    <div style="font-size:11px; color:#64748b; margin-top:8px;">
                                        Supports .md, .txt, .pdf, .json (Max 20MB per file)
                                    </div>
                                </label>

                                <div wire:loading wire:target="uploadedFiles" style="margin-top:12px; color:#3978f6; font-size:12px; font-weight:600;">
                                    ⏳ Uploading files, please wait...
                                </div>
                            </div>
                            @error('uploadedFiles') <span style="color:#dc2626; font-size:11.5px; display:block; margin-top:6px;">{{ $message }}</span> @enderror
                            @error('uploadedFiles.*') <span style="color:#dc2626; font-size:11.5px; display:block; margin-top:4px;">{{ $message }}</span> @enderror
                        </div>

                        <!-- Selected files preview list -->
                        @if(!empty($uploadedFiles))
                            <div style="margin-bottom:14px; background:#f1f5f9; padding:12px; border-radius:8px;">
                                <div style="font-size:12px; font-weight:700; color:#334155; margin-bottom:6px;">
                                    Selected {{ count($uploadedFiles) }} file(s) ready for indexing:
                                </div>
                                <ul style="margin:0; padding-left:18px; font-size:11.5px; color:#475569; max-height:120px; overflow-y:auto;">
                                    @foreach($uploadedFiles as $file)
                                        <li style="margin-bottom:2px;">
                                            <b>{{ $file->getClientOriginalName() }}</b> ({{ number_format($file->getSize() / 1024, 1) }} KB)
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <button
                                type="button"
                                wire:click="uploadFiles"
                                wire:loading.attr="disabled"
                                style="background:#16a34a; color:white; border:0; border-radius:7px; padding:9px 18px; font-size:12.5px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px;"
                            >
                                <span wire:loading.remove wire:target="uploadFiles">⚡ Parse, Chunk & Index Files</span>
                                <span wire:loading wire:target="uploadFiles">⚙️ Indexing Files into Vector Chunks...</span>
                            </button>
                        @endif
                    </div>
                @endif

                <!-- TAB 2: Direct Text Input Form -->
                @if($activeInputMode === 'text')
                    <form wire:submit="addTextDocument">
                        <div style="margin-bottom:12px;">
                            <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">
                                Document Title <span style="font-weight:400; color:#64748b;">(Optional / Jaruri nahi hai)</span>
                            </label>
                            <input
                                wire:model="docTitle"
                                type="text"
                                placeholder="Khali chhod sakte hain (e.g. Standard Website Pricing 2026)"
                                style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:8px 10px; font-size:12.5px;"
                            >
                            @error('docTitle') <span style="color:#dc2626; font-size:11px;">{{ $message }}</span> @enderror
                        </div>

                        <div style="margin-bottom:14px;">
                            <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Document Text / Markdown *</label>
                            <textarea
                                wire:model="docText"
                                rows="5"
                                placeholder="Paste product specifications, pricing matrices, FAQs, or markdown notes..."
                                style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:8px 10px; font-size:12.5px;"
                            ></textarea>
                            @error('docText') <span style="color:#dc2626; font-size:11px;">{{ $message }}</span> @enderror
                        </div>

                        <button
                            type="submit"
                            style="background:#3978f6; color:white; border:0; border-radius:7px; padding:8px 16px; font-size:12.5px; font-weight:700; cursor:pointer;"
                        >
                            Parse, Chunk & Index Text
                        </button>
                    </form>
                @endif
            </div>

            <!-- Indexed Documents List Table -->
            <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <h3 style="font-size:15px; font-weight:700; margin:0; color:#0f172a;">
                        Indexed Documents ({{ $knowledgeBase->documents->count() }})
                    </h3>
                    <span style="font-size:11px; color:#64748b;">
                        Each document can be active or disabled individually
                    </span>
                </div>

                <table style="width:100%; border-collapse:collapse; text-align:left; font-size:12.5px;">
                    <thead>
                        <tr style="border-bottom:1px solid #e2e8f0; background:#f8fafc; color:#64748b;">
                            <th style="padding:8px 10px;">TITLE & TYPE</th>
                            <th style="padding:8px 10px;">SIZE / CHUNKS</th>
                            <th style="padding:8px 10px;">STATUS</th>
                            <th style="padding:8px 10px; text-align:right;">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($knowledgeBase->documents as $doc)
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:10px;">
                                    <div style="display:flex; align-items:center; gap:6px;">
                                        @if($doc->source_type === 'file')
                                            <span style="font-size:10px; font-weight:700; padding:1px 5px; border-radius:4px; background:#eff6ff; color:#2563eb; text-transform:uppercase;">
                                                .{{ $doc->file_type ?: 'file' }}
                                            </span>
                                        @else
                                            <span style="font-size:10px; font-weight:700; padding:1px 5px; border-radius:4px; background:#f3f4f6; color:#4b5563;">
                                                TEXT
                                            </span>
                                        @endif
                                        <button
                                            type="button"
                                            wire:click="previewDocument({{ $doc->id }})"
                                            title="Click to view full content"
                                            style="background:transparent; border:0; padding:0; font-size:12.5px; font-weight:700; color:#0f172a; cursor:pointer; text-align:left; text-decoration:none;"
                                            onmouseover="this.style.color='#2563eb'; this.style.textDecoration='underline';"
                                            onmouseout="this.style.color='#0f172a'; this.style.textDecoration='none';"
                                        >
                                            {{ $doc->title }}
                                        </button>
                                    </div>
                                    <div style="font-size:11px; color:#64748b; margin-top:2px;">
                                        Added {{ $doc->created_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td style="padding:10px; font-family:monospace; font-weight:600;">
                                    {{ $doc->chunks->count() }} chunks
                                    @if($doc->file_size)
                                        <span style="font-size:11px; color:#94a3b8; font-weight:normal;">({{ number_format($doc->file_size / 1024, 1) }} KB)</span>
                                    @endif
                                </td>
                                <td style="padding:10px;">
                                    @if($doc->status === 'indexed')
                                        <button
                                            wire:click="toggleDocumentStatus({{ $doc->id }})"
                                            title="Click to turn off this document"
                                            style="background:#ecfdf5; color:#16a34a; font-size:10.5px; font-weight:700; padding:2px 8px; border-radius:10px; border:1px solid #a7f3d0; cursor:pointer;"
                                        >
                                            ● Active (On)
                                        </button>
                                    @else
                                        <button
                                            wire:click="toggleDocumentStatus({{ $doc->id }})"
                                            title="Click to activate this document"
                                            style="background:#f1f5f9; color:#64748b; font-size:10.5px; font-weight:700; padding:2px 8px; border-radius:10px; border:1px solid #cbd5e1; cursor:pointer;"
                                        >
                                            ⏸️ Inactive (Off)
                                        </button>
                                    @endif
                                </td>
                                <td style="padding:10px; text-align:right;">
                                    <button
                                        type="button"
                                        wire:click="previewDocument({{ $doc->id }})"
                                        style="padding:4px 9px; border:1px solid #bfdbfe; background:#eff6ff; color:#1d4ed8; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:4px; margin-right:4px;"
                                    >
                                        👁️ Dekhein / View
                                    </button>
                                    <button
                                        wire:confirm="Delete document '{{ $doc->title }}' and its vector chunks?"
                                        wire:click="deleteDocument({{ $doc->id }})"
                                        style="padding:4px 8px; border:1px solid #fee2e2; background:#fef2f2; color:#dc2626; border-radius:6px; font-size:11px; font-weight:600; cursor:pointer;"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center; padding:24px; color:#94a3b8;">
                                    No documents indexed in this collection yet. Upload files (.md, .txt) or paste text above.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Column: RAG Retrieval Test Simulator -->
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03); display:flex; flex-direction:column; justify-content:space-between;">
            <div>
                <h3 style="font-size:15px; font-weight:700; margin:0 0 6px; color:#0f172a;">RAG Retrieval Test Inspector</h3>
                <p style="color:#64748b; font-size:12px; margin:0 0 12px;">
                    Test vector similarity retrieval within this isolated business scope.
                </p>

                <input
                    wire:model="testQuery"
                    type="text"
                    placeholder="Enter search phrase or test question..."
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:12.5px; margin-bottom:10px;"
                >

                <button
                    wire:click="testRetrieval"
                    style="width:100%; background:#3978f6; color:white; border:0; border-radius:8px; padding:8px; font-size:12.5px; font-weight:700; cursor:pointer;"
                >
                    Run RAG Similarity Search
                </button>

                @if($retrievalResults !== null)
                    <div style="margin-top:16px;">
                        <div style="font-size:12px; font-weight:700; color:#334155; margin-bottom:8px;">
                            Retrieved Chunks ({{ count($retrievalResults) }}):
                        </div>
                        @forelse($retrievalResults as $res)
                            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px; margin-bottom:8px; font-size:11.5px;">
                                <div style="display:flex; justify-content:space-between; margin-bottom:4px; font-weight:700; color:#3568ca;">
                                    <span>{{ $res['document_title'] }}</span>
                                    <span>Score: {{ $res['score'] }}</span>
                                </div>
                                <div style="color:#475569; line-height:1.4;">
                                    {{ Str::limit($res['content'], 180) }}
                                </div>
                            </div>
                        @empty
                            <div style="font-size:12px; color:#94a3b8; font-style:italic;">
                                No relevant chunks matched the query in this business scope.
                            </div>
                        @endforelse
                    </div>
                @endif
            </div>

            <div style="border-top:1px solid #f1f5f9; padding-top:14px; margin-top:20px; font-size:11px; color:#94a3b8;">
                🔒 Zero Cross-Tenant Leakage: Searches only index chunks belonging to business #{{ $knowledgeBase->business_id }}.
            </div>
        </div>
    </div>

    <!-- Document Content Preview & Vector Chunks Modal -->
    @if($showDocModal && $previewData)
        <div style="position:fixed; inset:0; z-index:9999; display:flex; align-items:center; justify-content:center; background:rgba(15,23,42,0.65); backdrop-filter:blur(4px); padding:16px;">
            <div style="background:white; border-radius:18px; width:100%; max-width:820px; max-height:90vh; display:flex; flex-direction:column; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); overflow:hidden; border:1px solid #e2e8f0;">
                <!-- Modal Header -->
                <div style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:flex-start; background:#f8fafc;">
                    <div>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span style="font-size:10px; font-weight:800; padding:2px 6px; border-radius:5px; background:#dbeafe; color:#1e40af; text-transform:uppercase;">
                                .{{ $previewData['file_type'] ?: 'doc' }}
                            </span>
                            <h2 style="font-size:18px; font-weight:800; color:#0f172a; margin:0;">
                                {{ $previewData['title'] }}
                            </h2>
                        </div>
                        <div style="font-size:12px; color:#64748b; margin-top:4px; display:flex; align-items:center; gap:12px;">
                            <span>Source: <b>{{ ucfirst($previewData['source_type']) }}</b></span>
                            @if($previewData['file_size'])
                                <span>Size: <b>{{ $previewData['file_size'] }}</b></span>
                            @endif
                            <span>Chunks: <b>{{ $previewData['chunk_count'] }} vector blocks</b></span>
                            <span>Added: {{ $previewData['created_at'] }}</span>
                        </div>
                    </div>
                    <button wire:click="closeDocModal" style="background:#f1f5f9; border:0; width:32px; height:32px; border-radius:8px; font-size:16px; color:#64748b; cursor:pointer;" title="Close">✕</button>
                </div>
                <!-- Tabs -->
                <div style="display:flex; background:#f1f5f9; padding:6px 20px 0; border-bottom:1px solid #e2e8f0; gap:8px;">
                    <button type="button" wire:click="$set('previewTab', 'content')" style="padding:8px 16px; font-size:12.5px; font-weight:700; border-radius:8px 8px 0 0; border:1px solid #e2e8f0; border-bottom:0; cursor:pointer; {{ $previewTab === 'content' ? 'background:white; color:#0f172a;' : 'background:transparent; color:#64748b; border-color:transparent;' }}">
                        📄 Document Content (Jo Dala Gaya Hai)
                    </button>
                    <button type="button" wire:click="$set('previewTab', 'chunks')" style="padding:8px 16px; font-size:12.5px; font-weight:700; border-radius:8px 8px 0 0; border:1px solid #e2e8f0; border-bottom:0; cursor:pointer; {{ $previewTab === 'chunks' ? 'background:white; color:#0f172a;' : 'background:transparent; color:#64748b; border-color:transparent;' }}">
                        🧩 AI Vector Chunks ({{ count($previewData['chunks']) }})
                    </button>
                </div>
                <!-- Body -->
                <div style="padding:20px; overflow-y:auto; flex:1; background:#ffffff;">
                    @if($previewTab === 'content')
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                            <span style="font-size:12px; color:#64748b; font-weight:600;">Full Document Content ({{ strlen($previewData['raw_content']) }} characters)</span>
                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('raw-doc-text').innerText); this.innerText = '✓ Copied!'; setTimeout(() => this.innerText = '📋 Copy Text', 2000);" style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:6px; padding:4px 10px; font-size:11.5px; font-weight:600; color:#334155; cursor:pointer;">
                                📋 Copy Text
                            </button>
                        </div>
                        <div id="raw-doc-text" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:16px; font-size:13px; line-height:1.6; color:#1e293b; font-family:ui-monospace, monospace; white-space:pre-wrap; word-break:break-word; max-height:55vh; overflow-y:auto;">{{ $previewData['raw_content'] }}</div>
                    @else
                        <div style="display:flex; flex-direction:column; gap:12px; max-height:55vh; overflow-y:auto;">
                            @forelse($previewData['chunks'] as $chunk)
                                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px;">
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; border-bottom:1px dashed #e2e8f0; padding-bottom:6px;">
                                        <span style="font-size:11px; font-weight:800; color:#2563eb; background:#eff6ff; padding:2px 7px; border-radius:5px;">Chunk #{{ $chunk['index'] + 1 }}</span>
                                        <span style="font-size:11px; color:#94a3b8; font-family:monospace;">Tokens: ~{{ $chunk['token_count'] }}</span>
                                    </div>
                                    <div style="font-size:12.5px; color:#334155; line-height:1.5; white-space:pre-wrap; word-break:break-word;">{{ $chunk['content'] }}</div>
                                </div>
                            @empty
                                <div style="text-align:center; padding:30px; color:#94a3b8; font-size:12px;">No chunks generated.</div>
                            @endforelse
                        </div>
                    @endif
                </div>
                <!-- Footer -->
                <div style="padding:12px 20px; border-top:1px solid #e2e8f0; background:#f8fafc; display:flex; justify-content:flex-end;">
                    <button wire:click="closeDocModal" style="background:#334155; color:white; border:0; padding:8px 18px; border-radius:8px; font-size:12.5px; font-weight:700; cursor:pointer;">
                        Band Karein / Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

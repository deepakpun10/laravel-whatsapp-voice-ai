<div style="max-width:720px; margin:0 auto;">
    <div style="margin-bottom:20px;">
        <a href="{{ route('admin.knowledge-bases.index') }}" style="color:#3978f6; font-size:13px; text-decoration:none; font-weight:600;">
            ← Back to Knowledge Bases
        </a>
        <h1 style="font-size:24px; font-weight:800; margin:8px 0 4px; color:#0f172a;">Create Knowledge Base</h1>
        <p style="margin:0; color:#64748b; font-size:13.5px;">
            Upload files or create a knowledge collection for your AI agent. Files will be parsed and chunked automatically for AI replies.
        </p>
    </div>

    <form wire:submit="save" style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:26px; box-shadow:0 8px 24px rgba(0,0,0,.04);">
        <!-- Business Selector -->
        <div style="margin-bottom:18px;">
            <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                Owner Business *
            </label>
            <select
                wire:model="business_id"
                style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px; background:white;"
            >
                @foreach($businesses as $b)
                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                @endforeach
            </select>
            @error('business_id') <span style="color:#dc2626; font-size:11.5px;">{{ $message }}</span> @enderror
        </div>

        <!-- File Upload Section (Dropzone) -->
        <div style="margin-bottom:20px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:18px;">
            <label style="display:block; font-size:12.5px; font-weight:700; color:#0f172a; margin-bottom:4px;">
                📁 Upload Files (Optional)
            </label>
            <p style="font-size:11.5px; color:#64748b; margin:0 0 12px;">
                Supports <b>PDF, DOCX, TXT, Markdown (.md), CSV, JSON</b>. Multiple files can be selected at once.
            </p>

            <div style="border:2px dashed #cbd5e1; border-radius:10px; padding:22px; text-align:center; background:white;">
                <input
                    type="file"
                    wire:model="uploadedFiles"
                    multiple
                    accept=".pdf,.docx,.doc,.txt,.text,.md,.markdown,.csv,.json"
                    id="create_file_input"
                    style="display:none;"
                />
                <label for="create_file_input" style="cursor:pointer; display:inline-block;">
                    <div style="font-size:32px; margin-bottom:6px;">📄</div>
                    <span style="display:inline-block; background:#3978f6; color:white; font-size:12.5px; font-weight:700; padding:8px 18px; border-radius:8px; box-shadow:0 1px 3px rgba(57,120,246,0.3);">
                        Choose Files to Upload
                    </span>
                    <div style="font-size:11px; color:#64748b; margin-top:8px;">
                        Click here to select files (Max 20MB per file)
                    </div>
                </label>

                <div wire:loading wire:target="uploadedFiles" style="margin-top:10px; color:#3978f6; font-size:12px; font-weight:700;">
                    ⏳ Uploading file(s), please wait...
                </div>
            </div>

            <!-- Uploaded preview list -->
            @if(!empty($uploadedFiles))
                <div style="margin-top:14px; background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:12px 14px;">
                    <div style="font-size:12px; font-weight:700; color:#1e40af; margin-bottom:6px;">
                        ✓ {{ count($uploadedFiles) }} file(s) selected:
                    </div>
                    <ul style="margin:0; padding-left:18px; font-size:12px; color:#1e293b;">
                        @foreach($uploadedFiles as $file)
                            <li style="margin-bottom:3px;">
                                <b>{{ $file->getClientOriginalName() }}</b> ({{ number_format($file->getSize() / 1024, 1) }} KB)
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @error('uploadedFiles.*') <span style="color:#dc2626; font-size:11.5px; display:block; margin-top:4px;">{{ $message }}</span> @enderror
        </div>

        <!-- Collection Name (Optional if uploading file) -->
        <div style="margin-bottom:18px;">
            <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:6px;">
                <label style="font-size:12px; font-weight:700; color:#475569;">
                    Collection Title / Name <span style="font-weight:400; color:#64748b;">(Optional / Jaruri nahi hai)</span>
                </label>
                <span style="font-size:11px; color:#059669; font-weight:600;">
                    File ke naam se auto-set ho jayega
                </span>
            </div>
            <input
                wire:model="name"
                type="text"
                placeholder="Khali chhod sakte hain (e.g. Website Pricing, Product Catalog, etc.)"
                style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px; box-sizing:border-box;"
            />
            <span style="font-size:11px; color:#64748b; margin-top:4px; display:block;">
                Agar aap title nahi likhenge, toh uploaded file ke naam se automatic title ban jayega.
            </span>
            @error('name') <span style="color:#dc2626; font-size:11.5px;">{{ $message }}</span> @enderror
        </div>

        <!-- Status -->
        <div style="margin-bottom:18px;">
            <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                Status *
            </label>
            <select
                wire:model="status"
                style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px; background:white;"
            >
                <option value="active">Active (Available for AI RAG answers)</option>
                <option value="inactive">Inactive (Disabled / Band)</option>
            </select>
            @error('status') <span style="color:#dc2626; font-size:11.5px;">{{ $message }}</span> @enderror
        </div>

        <!-- Description -->
        <div style="margin-bottom:22px;">
            <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                Description / Purpose <span style="font-weight:400; color:#64748b;">(Optional)</span>
            </label>
            <textarea
                wire:model="description"
                rows="3"
                placeholder="Briefly describe what this knowledge base contains (e.g. Garlic wholesale grades, payment terms, or IT services)..."
                style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px; box-sizing:border-box;"
            ></textarea>
        </div>

        <!-- Buttons -->
        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <a href="{{ route('admin.knowledge-bases.index') }}" style="padding:9px 16px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none;">
                Cancel
            </a>
            <button
                type="submit"
                wire:loading.attr="disabled"
                style="padding:9px 22px; border:0; background:#3978f6; color:white; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer;"
            >
                <span wire:loading.remove wire:target="save">Save & Ingest Knowledge</span>
                <span wire:loading wire:target="save">⏳ Processing & Indexing...</span>
            </button>
        </div>
    </form>
</div>

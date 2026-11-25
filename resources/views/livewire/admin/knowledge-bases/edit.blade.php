<div style="max-width:700px; margin:0 auto;">
    <div style="margin-bottom:20px;">
        <a href="{{ route('admin.knowledge-bases.show', $knowledgeBase) }}" style="color:#3978f6; font-size:13px; text-decoration:none; font-weight:600;">
            ← Back to Collection
        </a>
        <h1 style="font-size:24px; font-weight:800; margin:8px 0 4px; color:#0f172a;">Edit Knowledge Base: {{ $knowledgeBase->name }}</h1>
        <p style="margin:0; color:#64748b; font-size:13.5px;">Update collection details, status, and description.</p>
    </div>

    <form wire:submit="update" style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:26px; box-shadow:0 8px 24px rgba(0,0,0,.04);">
        <div style="margin-bottom:16px;">
            <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                Collection Name *
            </label>
            <input
                wire:model="name"
                type="text"
                style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px;"
            >
            @error('name') <span style="color:#dc2626; font-size:11.5px;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                Status *
            </label>
            <select
                wire:model="status"
                style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px; background:white;"
            >
                <option value="active">Active (Available for RAG retrieval)</option>
                <option value="inactive">Inactive (Turned Off / Band hai)</option>
            </select>
            <span style="font-size:11px; color:#64748b; margin-top:4px; display:block;">
                Inactive collection ko AI agent RAG answers me use nahi karega.
            </span>
            @error('status') <span style="color:#dc2626; font-size:11.5px;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom:22px;">
            <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                Description / Purpose
            </label>
            <textarea
                wire:model="description"
                rows="3"
                style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px;"
            ></textarea>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <a href="{{ route('admin.knowledge-bases.show', $knowledgeBase) }}" style="padding:9px 16px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none;">
                Cancel
            </a>
            <button
                type="submit"
                style="padding:9px 20px; border:0; background:#3978f6; color:white; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;"
            >
                Save Changes
            </button>
        </div>
    </form>
</div>

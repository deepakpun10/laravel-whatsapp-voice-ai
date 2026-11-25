<div style="max-width:800px; margin:0 auto;">
    <div style="margin-bottom:20px;">
        <a href="{{ route('admin.businesses.index') }}" style="color:#3978f6; font-size:13px; text-decoration:none; font-weight:600;">
            ← Back to Businesses
        </a>
        <h1 style="font-size:24px; font-weight:800; margin:8px 0 4px; color:#0f172a;">Create New Business Workspace</h1>
        <p style="margin:0; color:#64748b; font-size:13.5px;">Each business gets strictly isolated contacts, AI agents, RAG knowledge collections, and routing rules.</p>
    </div>

    <form wire:submit="save" style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:26px; box-shadow:0 8px 24px rgba(0,0,0,.04);">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                    Business Name *
                </label>
                <input
                    wire:model="name"
                    type="text"
                    placeholder="e.g. Acme Tech Solutions"
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px;"
                >
                @error('name') <span style="color:#dc2626; font-size:11.5px; display:block; margin-top:3px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                    Business Code / ID
                </label>
                <input
                    wire:model="code"
                    type="text"
                    placeholder="e.g. ACM-001 (auto-generated if empty)"
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px;"
                >
                @error('code') <span style="color:#dc2626; font-size:11.5px; display:block; margin-top:3px;">{{ $message }}</span> @enderror
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                    Category / Industry
                </label>
                <input
                    wire:model="category"
                    type="text"
                    placeholder="e.g. IT Services, Agri-Wholesale, Retail"
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px;"
                >
                @error('category') <span style="color:#dc2626; font-size:11.5px; display:block; margin-top:3px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                    Status *
                </label>
                <select
                    wire:model="status"
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px; background:white;"
                >
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                @error('status') <span style="color:#dc2626; font-size:11.5px; display:block; margin-top:3px;">{{ $message }}</span> @enderror
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                    Timezone
                </label>
                <input
                    wire:model="timezone"
                    type="text"
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px;"
                >
                @error('timezone') <span style="color:#dc2626; font-size:11.5px; display:block; margin-top:3px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                    Primary Communication Language
                </label>
                <select
                    wire:model="default_language"
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px; background:white;"
                >
                    <option value="hi_en">Hinglish / Hindi & English</option>
                    <option value="en">English Only</option>
                    <option value="hi">Hindi Only</option>
                </select>
                @error('default_language') <span style="color:#dc2626; font-size:11.5px; display:block; margin-top:3px;">{{ $message }}</span> @enderror
            </div>
        </div>

        <div style="margin-bottom:22px;">
            <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                Description
            </label>
            <textarea
                wire:model="description"
                rows="3"
                placeholder="Describe what this business sells, products/services offered..."
                style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px;"
            ></textarea>
            @error('description') <span style="color:#dc2626; font-size:11.5px; display:block; margin-top:3px;">{{ $message }}</span> @enderror
        </div>

        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:12px; color:#475569; line-height:1.5;">
            🔒 <b>Tenant Isolation Assurance</b>: Creating a business provisions a dedicated scope. AI Agents and Knowledge Bases linked to this business will never be exposed to other tenants, even when sharing a single WhatsApp telephone line.
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <a href="{{ route('admin.businesses.index') }}" style="padding:9px 16px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none;">
                Cancel
            </a>
            <button
                type="submit"
                style="padding:9px 20px; border:0; background:#3978f6; color:white; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;"
            >
                Create Business
            </button>
        </div>
    </form>
</div>

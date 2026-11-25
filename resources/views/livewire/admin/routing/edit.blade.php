<div style="max-width:700px; margin:0 auto;">
    <div style="margin-bottom:20px;">
        <a href="{{ route('admin.routing.show', $rule) }}" style="color:#3978f6; font-size:13px; text-decoration:none; font-weight:600;">
            ← Back to Rule Details
        </a>
        <h1 style="font-size:24px; font-weight:800; margin:8px 0 4px; color:#0f172a;">Edit Routing Rule: {{ $rule->name }}</h1>
        <p style="margin:0; color:#64748b; font-size:13.5px;">Update keywords, priority, and minimum confidence requirements.</p>
    </div>

    <form wire:submit="update" style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:26px; box-shadow:0 8px 24px rgba(0,0,0,.04);">
        <div style="margin-bottom:16px;">
            <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                Destination Business *
            </label>
            <select
                wire:model="business_id"
                style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px; background:white;"
            >
                @foreach($businesses as $b)
                    <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->code ?? 'BIZ' }})</option>
                @endforeach
            </select>
            @error('business_id') <span style="color:#dc2626; font-size:11.5px; display:block; margin-top:3px;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                Rule Name *
            </label>
            <input
                wire:model="name"
                type="text"
                style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px;"
            >
            @error('name') <span style="color:#dc2626; font-size:11.5px; display:block; margin-top:3px;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                Matching Keywords / Phrases (Comma separated) *
            </label>
            <textarea
                wire:model="keywords"
                rows="3"
                style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px;"
            ></textarea>
            @error('keywords') <span style="color:#dc2626; font-size:11.5px; display:block; margin-top:3px;">{{ $message }}</span> @enderror
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">
            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                    Rule Priority *
                </label>
                <select
                    wire:model="priority"
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px; background:white;"
                >
                    <option value="high">High (Weight: 1.5x)</option>
                    <option value="normal">Normal (Weight: 1.0x)</option>
                    <option value="low">Low (Weight: 0.7x)</option>
                </select>
            </div>

            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                    Minimum Confidence Threshold *
                </label>
                <select
                    wire:model="min_confidence"
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px; background:white;"
                >
                    <option value="0.80">0.80 (Balanced)</option>
                    <option value="0.85">0.85 (Strict - Recommended)</option>
                    <option value="0.90">0.90 (Very Strict)</option>
                    <option value="0.95">0.95 (Ultra Conservative)</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom:22px;">
            <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:#334155; cursor:pointer;">
                <input wire:model="is_active" type="checkbox" style="width:16px; height:16px;">
                Rule is active and evaluated for incoming enquiries
            </label>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <a href="{{ route('admin.routing.show', $rule) }}" style="padding:9px 16px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none;">
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

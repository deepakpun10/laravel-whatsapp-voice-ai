<div style="max-width:800px; margin:0 auto;">
    <div style="margin-bottom:20px;">
        <a href="{{ route('admin.contacts.index') }}" style="color:#3978f6; font-size:13px; text-decoration:none; font-weight:600;">
            ← Back to Contacts
        </a>
        <h1 style="font-size:24px; font-weight:800; margin:8px 0 4px; color:#0f172a;">Add Lead / Contact</h1>
        <p style="margin:0; color:#64748b; font-size:13.5px;">Register a new customer or business lead manually into CRM.</p>
    </div>

    <form wire:submit="save" style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:26px; box-shadow:0 8px 24px rgba(0,0,0,.04);">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                    Contact Name *
                </label>
                <input
                    wire:model="name"
                    type="text"
                    placeholder="e.g. Rahul Sharma"
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px;"
                >
                @error('name') <span style="color:#dc2626; font-size:11.5px; display:block; margin-top:3px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                    Phone Number (WhatsApp) *
                </label>
                <input
                    wire:model="phone"
                    type="text"
                    placeholder="e.g. +919876543210"
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px;"
                >
                @error('phone') <span style="color:#dc2626; font-size:11.5px; display:block; margin-top:3px;">{{ $message }}</span> @enderror
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                    Assigned Business
                </label>
                <select
                    wire:model="business_id"
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px; background:white;"
                >
                    <option value="">-- Unassigned (Needs Review) --</option>
                    @foreach($businesses as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
                @error('business_id') <span style="color:#dc2626; font-size:11.5px; display:block; margin-top:3px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                    Lead Status *
                </label>
                <select
                    wire:model="lead_status"
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px; background:white;"
                >
                    <option value="new">New</option>
                    <option value="interested">Interested</option>
                    <option value="qualified">Qualified</option>
                    <option value="follow_up">Follow-up</option>
                    <option value="converted">Converted</option>
                    <option value="lost">Lost</option>
                </select>
                @error('lead_status') <span style="color:#dc2626; font-size:11.5px; display:block; margin-top:3px;">{{ $message }}</span> @enderror
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                    Email Address
                </label>
                <input
                    wire:model="email"
                    type="email"
                    placeholder="customer@example.com"
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px;"
                >
                @error('email') <span style="color:#dc2626; font-size:11.5px; display:block; margin-top:3px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                    Location / City
                </label>
                <input
                    wire:model="location"
                    type="text"
                    placeholder="e.g. Solan, Delhi, Mumbai"
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px;"
                >
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                    Requirement / Enquiry Details
                </label>
                <input
                    wire:model="requirement"
                    type="text"
                    placeholder="e.g. Catalog Website or 500kg Garlic Supply"
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px;"
                >
            </div>

            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                    Budget / Order Volume
                </label>
                <input
                    wire:model="budget"
                    type="text"
                    placeholder="e.g. ₹50,000 or 5 Quintals"
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px;"
                >
            </div>
        </div>

        <div style="margin-bottom:22px;">
            <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">
                Internal Notes
            </label>
            <textarea
                wire:model="notes"
                rows="3"
                placeholder="Initial context, customer preferences, or handover instructions..."
                style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:13px;"
            ></textarea>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <a href="{{ route('admin.contacts.index') }}" style="padding:9px 16px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none;">
                Cancel
            </a>
            <button
                type="submit"
                style="padding:9px 20px; border:0; background:#3978f6; color:white; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;"
            >
                Save Contact
            </button>
        </div>
    </form>
</div>

<div>
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:22px; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 style="font-size:24px; font-weight:800; margin:0 0 6px; color:#0f172a;">Campaigns</h1>
            <p style="margin:0; color:#64748b; font-size:13.5px;">Manage approved Meta WhatsApp templates and consent-based voice campaigns.</p>
        </div>
    </div>

    <!-- Campaigns Grid and Form -->
    <div style="display:grid; grid-template-columns:1.5fr 1fr; gap:20px;">
        <div style="display:flex; flex-direction:column; gap:16px;">
            @forelse($campaigns as $camp)
                <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <span style="font-size:10.5px; font-weight:700; padding:2px 7px; border-radius:12px; background:#f1f5f9; color:#475569; text-transform:uppercase;">
                                {{ $camp->status }}
                            </span>
                            <h3 style="font-size:16px; font-weight:700; margin:8px 0 2px; color:#0f172a;">{{ $camp->name }}</h3>
                            <div style="font-size:12px; color:#64748b;">
                                🏢 {{ $camp->business->name }} · Channel: {{ ucfirst(str_replace('_', ' ', $camp->channel)) }}
                            </div>
                        </div>
                    </div>

                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:14px; border-top:1px solid #f1f5f9; padding-top:12px; font-size:12.5px;">
                        <span>Recipients: <b>{{ $camp->total_recipients }}</b></span>
                        <span>Delivered: <b>{{ $camp->delivered_count }}</b></span>
                        <span>Replies: <b>{{ $camp->reply_count }}</b></span>
                    </div>
                </div>
            @empty
                <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:36px; text-align:center; color:#94a3b8; font-size:13px;">
                    No campaigns created yet. Use the form on the right to draft a campaign.
                </div>
            @endforelse
        </div>

        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03); height:fit-content;">
            <h3 style="font-size:16px; font-weight:700; margin:0 0 12px; color:#0f172a;">Draft New Campaign</h3>
            <form wire:submit="createCampaign">
                <div style="margin-bottom:12px;">
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Campaign Name *</label>
                    <input wire:model="name" type="text" placeholder="e.g. Website Enquiry Follow-up" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:8px 10px; font-size:12.5px;">
                    @error('name') <span style="color:#dc2626; font-size:11px;">{{ $message }}</span> @enderror
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
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Channel *</label>
                    <select wire:model="channel" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:8px 10px; font-size:12.5px; background:white;">
                        <option value="whatsapp_template">Approved WhatsApp Template</option>
                        <option value="voice_callback">Voice Callback Broadcast</option>
                    </select>
                </div>

                <div style="margin-bottom:18px;">
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">Template Name</label>
                    <input wire:model="template_name" type="text" placeholder="e.g. website_enquiry_followup" style="width:100%; border:1px solid #cbd5e1; border-radius:7px; padding:8px 10px; font-size:12.5px;">
                </div>

                <button type="submit" style="width:100%; background:#3978f6; color:white; border:0; border-radius:7px; padding:9px; font-size:13px; font-weight:700; cursor:pointer;">
                    Save Draft Campaign
                </button>
            </form>
        </div>
    </div>
</div>

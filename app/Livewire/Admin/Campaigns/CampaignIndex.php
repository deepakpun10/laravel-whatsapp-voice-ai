<?php

namespace App\Livewire\Admin\Campaigns;

use App\Models\Business;
use App\Models\Campaign;
use App\Services\TenantContext;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Campaigns', 'breadcrumb' => 'Workspace / Campaigns'])]
class CampaignIndex extends Component
{
    public string $name = '';

    public ?int $business_id = null;

    public string $channel = 'whatsapp_template';

    public string $template_name = '';

    public function mount(): void
    {
        $activeId = TenantContext::getActiveBusinessId();
        if ($activeId) {
            $this->business_id = $activeId;
        } else {
            $this->business_id = Business::first()?->id;
        }
    }

    public function createCampaign(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'business_id' => 'required|exists:businesses,id',
            'channel' => 'required|in:whatsapp_template,voice_callback',
        ]);

        Campaign::create([
            'business_id' => $this->business_id,
            'name' => $this->name,
            'channel' => $this->channel,
            'template_name' => $this->template_name ?: 'sales_followup_v1',
            'status' => 'draft',
            'total_recipients' => 0,
        ]);

        $this->reset(['name', 'template_name']);
        $this->dispatch('toast', message: 'Campaign saved as draft.');
    }

    public function render()
    {
        $activeBusinessId = TenantContext::getActiveBusinessId();
        $campaigns = Campaign::with('business')
            ->when($activeBusinessId, fn ($q) => $q->where('business_id', $activeBusinessId))
            ->latest()
            ->get();

        return view('livewire.admin.campaigns.index', [
            'campaigns' => $campaigns,
            'businesses' => Business::where('status', 'active')->get(),
        ]);
    }
}

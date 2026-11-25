<?php

namespace App\Livewire\Admin\Calls;

use App\Models\Business;
use App\Models\Call;
use App\Services\TenantContext;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.admin', ['title' => 'Voice Calls Intelligence', 'breadcrumb' => 'Workspace / Voice Calls'])]
class CallIndex extends Component
{
    use WithPagination;

    public string $viewMode = 'grouped'; // 'grouped' or 'all'

    public string $search = '';

    public string $businessFilter = 'all';

    public string $directionFilter = 'all';

    public string $outcomeFilter = 'all';

    public string $datePreset = 'all'; // 'all', 'today', '7_days', '30_days', 'custom'

    public string $fromDate = '';

    public string $toDate = '';

    public array $selectedNumbers = [];

    public bool $selectAll = false;

    public ?string $expandedNumber = null;

    public function mount(): void
    {
        $activeId = TenantContext::getActiveBusinessId();
        if ($activeId) {
            $this->businessFilter = (string) $activeId;
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedBusinessFilter(): void
    {
        $this->resetPage();
    }

    public function updatedDirectionFilter(): void
    {
        $this->resetPage();
    }

    public function updatedOutcomeFilter(): void
    {
        $this->resetPage();
    }

    public function setPreset(string $preset): void
    {
        $this->datePreset = $preset;
        if ($preset === 'today') {
            $this->fromDate = Carbon::today()->toDateString();
            $this->toDate = Carbon::today()->toDateString();
        } elseif ($preset === '7_days') {
            $this->fromDate = Carbon::now()->subDays(7)->toDateString();
            $this->toDate = Carbon::today()->toDateString();
        } elseif ($preset === '30_days') {
            $this->fromDate = Carbon::now()->subDays(30)->toDateString();
            $this->toDate = Carbon::today()->toDateString();
        } elseif ($preset === 'all') {
            $this->fromDate = '';
            $this->toDate = '';
        }
        $this->resetPage();
    }

    public function toggleExpand(string $phoneNumber): void
    {
        $this->expandedNumber = ($this->expandedNumber === $phoneNumber) ? null : $phoneNumber;
    }

    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            if ($this->viewMode === 'grouped') {
                $this->selectedNumbers = $this->getFilteredQuery()
                    ->select('to_number')
                    ->distinct()
                    ->pluck('to_number')
                    ->toArray();
            } else {
                $this->selectedNumbers = $this->getFilteredQuery()
                    ->pluck('id')
                    ->map(fn ($id) => (string) $id)
                    ->toArray();
            }
        } else {
            $this->selectedNumbers = [];
        }
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->businessFilter = 'all';
        $this->directionFilter = 'all';
        $this->outcomeFilter = 'all';
        $this->datePreset = 'all';
        $this->fromDate = '';
        $this->toDate = '';
        $this->selectedNumbers = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function getFilteredQuery()
    {
        $query = Call::with(['contact', 'business', 'apiConnection', 'aiAgent']);

        if ($this->businessFilter !== 'all') {
            $query->where('business_id', $this->businessFilter);
        }

        if ($this->directionFilter !== 'all') {
            $query->where('direction', $this->directionFilter);
        }

        if ($this->outcomeFilter !== 'all') {
            $query->where('outcome', $this->outcomeFilter);
        }

        if (! empty(trim($this->search))) {
            $term = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($term) {
                $q->where('to_number', 'like', $term)
                    ->orWhere('from_number', 'like', $term)
                    ->orWhereHas('contact', fn ($cq) => $cq->where('name', 'like', $term));
            });
        }

        if ($this->datePreset === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($this->datePreset === '7_days') {
            $query->where('created_at', '>=', Carbon::now()->subDays(7));
        } elseif ($this->datePreset === '30_days') {
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        } elseif (! empty($this->fromDate) || ! empty($this->toDate)) {
            if (! empty($this->fromDate)) {
                $query->whereDate('created_at', '>=', $this->fromDate);
            }
            if (! empty($this->toDate)) {
                $query->whereDate('created_at', '<=', $this->toDate);
            }
        }

        if (! empty($this->selectedNumbers)) {
            if ($this->viewMode === 'grouped') {
                $query->whereIn('to_number', $this->selectedNumbers);
            } else {
                $query->whereIn('id', $this->selectedNumbers);
            }
        }

        return $query;
    }

    public function exportCsv(): StreamedResponse
    {
        // When exporting, if specific items were selected, export those, otherwise export all matching filters
        $calls = $this->getFilteredQuery()->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="voice_calls_export_'.date('Y-m-d_His').'.csv"',
        ];

        $callback = function () use ($calls) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF"); // UTF-8 BOM for Microsoft Excel

            // Prevent CSV Injection (Formula Injection / CWE-1236)
            $sanitizeCell = function ($val): string {
                $str = (string) ($val ?? '');
                if ($str !== '' && in_array($str[0], ['=', '+', '-', '@', "\t", "\r"])) {
                    return "'".$str;
                }

                return $str;
            };

            fputcsv($file, [
                'Call ID',
                'Phone Number',
                'Customer / Lead Name',
                'Business Workspace',
                'AI Voice Agent',
                'Date & Time (IST)',
                'Direction',
                'Duration (Seconds)',
                'Duration (Formatted)',
                'Status',
                'Outcome / Intent',
                'AI Call Summary',
                'Customer Requirements',
                'Full Transcript',
                'Recording URL',
            ]);

            foreach ($calls as $c) {
                $facts = '';
                if (! empty($c->extracted_facts) && is_array($c->extracted_facts)) {
                    $facts = collect($c->extracted_facts)
                        ->map(fn ($v, $k) => "$k: ".(is_array($v) ? json_encode($v) : $v))
                        ->implode(' | ');
                }

                $timeStr = $c->started_at
                    ? $c->started_at->timezone('Asia/Kolkata')->format('d M Y, h:i A')
                    : $c->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A');

                fputcsv($file, [
                    $c->id,
                    $sanitizeCell($c->to_number),
                    $sanitizeCell($c->contact?->name ?? 'External Lead'),
                    $sanitizeCell($c->business?->name ?? 'Unassigned'),
                    $sanitizeCell($c->aiAgent?->name ?? 'Default Sarvam Agent'),
                    $timeStr,
                    ucfirst($c->direction),
                    $c->duration_seconds,
                    gmdate('i:s', $c->duration_seconds),
                    ucfirst($c->status),
                    $sanitizeCell(str_replace('_', ' ', $c->outcome ?: $c->status)),
                    $sanitizeCell($c->summary ?: ''),
                    $sanitizeCell($facts),
                    $sanitizeCell($c->transcript ?: ''),
                    $sanitizeCell($c->recording_url ?: ''),
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, 'voice_calls_export_'.date('Y-m-d_His').'.csv', $headers);
    }

    public function render()
    {
        // Auto-resolve stale initiated/ringing calls older than 10 minutes
        Call::whereIn('status', ['initiated', 'ringing'])
            ->where('created_at', '<', now()->subMinutes(10))
            ->update([
                'status' => 'missed',
                'outcome' => 'no_answer',
            ]);

        $totalCalls = Call::count();
        $completedCalls = Call::where('status', 'completed')->count();
        $interestedCalls = Call::whereIn('outcome', ['interested', 'meeting_scheduled', 'callback_requested'])->count();

        // 1. Grouped View Logic
        $groupedRecords = [];
        $flatCalls = null;

        if ($this->viewMode === 'grouped') {
            // Group by to_number
            $allFilteredCalls = $this->getFilteredQuery()->latest()->get();

            $byPhone = $allFilteredCalls->groupBy('to_number');
            foreach ($byPhone as $phone => $phoneCalls) {
                $latest = $phoneCalls->first();
                $groupedRecords[] = [
                    'phone_number' => $phone,
                    'contact' => $latest->contact,
                    'business' => $latest->business,
                    'calls_count' => $phoneCalls->count(),
                    'latest_call' => $latest,
                    'total_duration' => $phoneCalls->sum('duration_seconds'),
                    'latest_outcome' => $latest->outcome ?: $latest->status,
                    'latest_summary' => $latest->summary,
                    'calls' => $phoneCalls,
                ];
            }
        } else {
            // 2. Flat List View
            $flatCalls = $this->getFilteredQuery()->latest()->paginate(15);
        }

        return view('livewire.admin.calls.index', [
            'groupedRecords' => $groupedRecords,
            'calls' => $flatCalls,
            'businesses' => Business::where('status', 'active')->orderBy('name')->get(),
            'totalCalls' => $totalCalls,
            'completedCalls' => $completedCalls,
            'interestedCalls' => $interestedCalls,
            'uniqueNumbersCount' => Call::distinct('to_number')->count('to_number'),
        ]);
    }
}

<div class="relative inline-block">
    <select
        wire:model.live="selectedBusinessId"
        class="border border-slate-200 bg-white text-slate-800 text-xs font-semibold rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition"
        style="min-width: 170px;"
    >
        <option value="all">🏢 All Businesses</option>
        @foreach($businesses as $business)
            <option value="{{ $business->id }}">
                {{ $business->name }} ({{ $business->code ?? 'BIZ' }})
            </option>
        @endforeach
    </select>
</div>

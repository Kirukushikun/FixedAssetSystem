<div>
    <h3 class="text-center font-bold text-xl mb-1">Select a Pending Item</h3>
    <p class="text-center text-gray-400 text-sm mb-6">Items already received from the Purchasing System, awaiting registration as a Fixed Asset.</p>

    @if($loading || !$loaded)
        <div class="flex flex-col items-center justify-center gap-3 py-10 text-gray-400">
            <i class="fa-solid fa-spinner fa-spin text-2xl"></i>
            <span class="text-sm font-semibold">Loading pending items...</span>
        </div>
    @elseif($error)
        <div class="flex flex-col items-center justify-center gap-3 py-10 text-center">
            <i class="fa-solid fa-triangle-exclamation text-2xl text-orange-400"></i>
            <p class="text-sm font-semibold text-gray-600">{{ $error }}</p>
            <button wire:click="retry"
                class="mt-1 px-4 py-2 bg-[#4fd1c5] hover:bg-teal-500 text-white rounded-lg text-xs font-bold transition-colors">
                <i class="fa-solid fa-rotate-right"></i> Retry
            </button>
        </div>
    @elseif(empty($items))
        <div class="flex flex-col items-center justify-center gap-3 py-10 text-center text-gray-400">
            <i class="fa-solid fa-box-open text-2xl"></i>
            <span class="text-sm font-semibold">No pending items awaiting registration.</span>
        </div>
    @else
        <div class="space-y-3 max-h-[420px] overflow-y-auto pr-1">
            @foreach($items as $item)
                <div class="border border-gray-200 rounded-xl p-4 hover:border-teal-400 transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-bold text-sm text-gray-700">{{ $item['description'] ?? 'Untitled item' }}</p>
                            <p class="text-xs text-gray-400 font-semibold mt-0.5">{{ $item['reference_id'] }}</p>
                        </div>
                        <button wire:click="selectItem('{{ $item['reference_id'] }}')"
                            wire:loading.attr="disabled"
                            class="shrink-0 px-3 py-1.5 bg-[#4fd1c5] hover:bg-teal-500 text-white rounded-lg text-xs font-bold transition-colors disabled:opacity-50">
                            Select
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-x-4 gap-y-1 mt-3 text-xs text-gray-500">
                        <p><span class="font-semibold text-gray-600">PR:</span> {{ $item['requisition_number'] ?? '—' }}</p>
                        <p><span class="font-semibold text-gray-600">PO:</span> {{ $item['purchase_order']['number'] ?? '—' }}</p>
                        <p><span class="font-semibold text-gray-600">Farm:</span> {{ $item['farm']['name'] ?? '—' }}</p>
                        <p><span class="font-semibold text-gray-600">Department:</span> {{ $item['department']['name'] ?? '—' }}</p>
                        <p><span class="font-semibold text-gray-600">Supplier:</span> {{ $item['supplier']['name'] ?? '—' }}</p>
                        <p><span class="font-semibold text-gray-600">Cost:</span>
                            {{ $item['currency'] ?? 'PHP' }} {{ number_format((float) ($item['actual_cost'] ?? $item['estimated_cost'] ?? 0), 2) }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@props(['paginator'])

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $paginator */
    $currentPage = $paginator->currentPage();
    $lastPage    = $paginator->lastPage();

    $start = max(1, $currentPage - 2);
    $end   = min($lastPage, $start + 4);

    if ($end - $start < 4) {
        $start = max(1, $end - 4);
    }

    $pages = range($start, $end);
@endphp

<div class="pagination-container flex items-center justify-end gap-3 mt-auto">
    <div class="text-xs text-gray-400">
        Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
    </div>

    {{-- Previous --}}
    @if ($currentPage > 1)
        <button
            wire:click="goToPage({{ $currentPage - 1 }})"
            class="px-2 py-2 rounded-md bg-teal-100 hover:scale-110 cursor-pointer text-sm">
            <i class="fa-solid fa-caret-left text-teal-500"></i>
        </button>
    @endif

    {{-- Page Numbers --}}
    @foreach ($pages as $i)
        <button
            wire:click="goToPage({{ $i }})"
            class="px-4 py-2 rounded-md text-sm hover:scale-110 cursor-pointer
            {{ $i == $currentPage
                ? 'bg-teal-400 text-white'
                : 'bg-teal-100 text-teal-500'
            }}">
            {{ $i }}
        </button>
    @endforeach

    {{-- Next --}}
    @if ($currentPage < $lastPage)
        <button
            wire:click="goToPage({{ $currentPage + 1 }})"
            class="px-2 py-2 rounded-md bg-teal-100 hover:scale-110 cursor-pointer text-sm">
            <i class="fa-solid fa-caret-right text-teal-500"></i>
        </button>
    @endif
</div>
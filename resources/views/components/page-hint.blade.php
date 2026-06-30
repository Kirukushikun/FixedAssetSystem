@props(['type' => 'what', 'title' => ''])

@php
    $label = match($type) {
        'help'  => 'Help / Required Fields',
        'about' => 'About this Module',
        default => 'What am I looking at?',
    };
@endphp

<div x-data="{ open: false, px: 0, py: 0 }"
     class="self-center shrink-0"
     @click.outside="open = false"
     @scroll.window="open = false"
     @resize.window="open = false">

    <button type="button"
        @click="let r = $el.getBoundingClientRect(); px = r.left; py = r.bottom + 6; open = !open"
        :class="open ? 'border-sky-400 bg-sky-50 text-sky-700' : 'border-sky-200 bg-white text-sky-500 hover:border-sky-300 hover:bg-sky-50'"
        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold rounded-full border transition-colors">
        <i class="fa-solid fa-circle-info text-[10px]"></i>
        <span>{{ $label }}</span>
    </button>

    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         :style="`top:${py}px; left:${px}px`"
         class="fixed z-[65] w-80 max-w-[calc(100vw-32px)] bg-sky-50 border border-sky-200 rounded-xl p-4 shadow-sm">

        <div class="flex items-start justify-between gap-3 mb-2.5">
            <p class="text-[10px] font-bold text-sky-700 uppercase tracking-widest leading-snug">{{ $title }}</p>
            <button type="button"
                @click="open = false"
                class="text-sky-400 hover:text-sky-700 transition-colors shrink-0">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>

        <ul class="text-xs text-sky-700 space-y-1.5 leading-relaxed list-disc list-outside pl-3.5">
            {{ $slot }}
        </ul>
    </div>
</div>

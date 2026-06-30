<div class="relative" x-data="{ open: @entangle('showDropdown').live }" @click.outside="open = false; $wire.showDropdown = false">

    {{-- Bell button --}}
    <button
        wire:click="toggleDropdown"
        class="relative w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors"
        title="Notifications">
        <i class="fa-solid fa-bell text-gray-500 text-base"></i>
        @if($unreadCount > 0)
            <span class="absolute top-0.5 right-0.5 min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center px-0.5">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute right-0 top-11 w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 z-[200] overflow-hidden"
        style="display:none">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <span class="font-bold text-gray-800 text-sm">Notifications</span>
            @if($unreadCount > 0)
                <button wire:click="markAllRead" class="text-xs text-teal-600 font-semibold hover:text-teal-700">
                    Mark all read
                </button>
            @endif
        </div>

        {{-- List --}}
        <div class="max-h-[420px] overflow-y-auto divide-y divide-gray-50">
            @forelse($notifications as $notif)
                @php
                    $isUnread = is_null($notif['read_at']);
                    $icon = match($notif['type']) {
                        'disposal'  => 'fa-recycle text-orange-500',
                        'transfer'  => 'fa-arrow-right-arrow-left text-indigo-500',
                        'lost'      => 'fa-magnifying-glass text-red-500',
                        default     => 'fa-bell text-gray-400',
                    };
                @endphp
                <div class="flex items-start gap-3 px-4 py-3 {{ $isUnread ? 'bg-teal-50' : 'hover:bg-gray-50' }} transition-colors">
                    <div class="w-8 h-8 rounded-full {{ $isUnread ? 'bg-teal-100' : 'bg-gray-100' }} flex items-center justify-center shrink-0 mt-0.5">
                        <i class="fa-solid {{ $icon }} text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        @if(!empty($notif['url']))
                            <a href="{{ $notif['url'] }}" wire:click="markRead({{ $notif['id'] }})"
                                class="text-sm font-semibold text-gray-800 hover:text-teal-600 leading-snug block truncate">
                                {{ $notif['title'] }}
                            </a>
                        @else
                            <p class="text-sm font-semibold text-gray-800 leading-snug">{{ $notif['title'] }}</p>
                        @endif
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ $notif['message'] }}</p>
                        <p class="text-[10px] text-gray-400 mt-1">
                            {{ \Carbon\Carbon::parse($notif['created_at'])->diffForHumans() }}
                        </p>
                    </div>
                    <div class="flex flex-col items-end gap-1 shrink-0">
                        @if($isUnread)
                            <button wire:click="markRead({{ $notif['id'] }})" title="Mark as read"
                                class="w-2 h-2 bg-teal-500 rounded-full hover:bg-teal-600 mt-1.5 shrink-0"></button>
                        @endif
                        <button wire:click="dismiss({{ $notif['id'] }})" title="Dismiss"
                            class="text-gray-300 hover:text-gray-500 text-xs mt-1">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="px-4 py-10 flex flex-col items-center justify-center gap-2 text-center">
                    <i class="fa-regular fa-bell-slash text-2xl text-gray-300"></i>
                    <p class="text-sm text-gray-400">No notifications yet</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="flex gap-3 items-center">
    @if(Auth::user()->is_admin)
        <div class="flex items-center gap-2">
            <span class="text-xs font-medium text-gray-700">Snipe Sync</span>
            <button 
                wire:click="toggleSync"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 {{ $enableSync ? 'bg-teal-500' : 'bg-gray-300' }}"
            >
                <span 
                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $enableSync ? 'translate-x-6' : 'translate-x-1' }}"
                ></span>
            </button>
        </div>
    @endif
    
    <button class="px-5 py-2 bg-red-500 rounded-lg font-bold text-white text-xs hover:bg-red-600" 
            onclick="window.location.href='/logout'">LOGOUT</button>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 bg-black/30 z-40" wire:click="closeModal"></div>
    
    <div class="fixed inset-0 flex items-center justify-center z-50 p-4" wire:click.self="closeModal">
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full">
            <h2 class="text-xl font-semibold mb-3">Enable Snipe Sync?</h2>
            <p class="text-gray-600 mb-6">Are you sure you want to enable snipe sync? This will start synchronizing all IT related assets.</p>

            <div class="flex justify-end gap-3">
                <button wire:click="closeModal" class="px-4 py-2 border rounded hover:bg-gray-100">Cancel</button>
                <button wire:click="confirmEnable" class="px-4 py-2 bg-teal-500 text-white rounded hover:bg-teal-600">
                    Confirm
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
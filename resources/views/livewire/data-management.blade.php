<div class="h-full flex flex-col gap-5 overflow-y-auto minimal-scroll pr-1"
     x-data="{
         showModal: false,
         modal: { section: '', count: 0 },
         confirmInput: '',
         get canConfirm() {
             return this.confirmInput !== '' && this.confirmInput === String(this.modal.count);
         },
         openModal(section, count) {
             this.modal = { section, count };
             this.confirmInput = '';
             this.showModal = true;
             this.$nextTick(() => { if (this.$refs.confirmInput) this.$refs.confirmInput.focus(); });
         },
         doConfirm() {
             if (!this.canConfirm) return;
             const s = this.modal.section;
             this.showModal = false;
             this.confirmInput = '';
             if (s === 'audit')  $wire.executeAudit();
             else if (s === 'attach') $wire.executeAttach();
             else if (s === 'log')    $wire.executeLog();
         }
     }"
     @keydown.escape.window="showModal = false">

    {{-- ── Warning Banner ── --}}
    <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 flex items-start gap-3 shrink-0">
        <div class="shrink-0 w-9 h-9 rounded-full bg-red-100 flex items-center justify-center">
            <i class="fa-solid fa-triangle-exclamation text-red-500 text-sm"></i>
        </div>
        <div>
            <p class="text-sm font-bold text-red-700">Destructive Actions Ahead</p>
            <p class="text-xs text-red-500 mt-0.5">Actions on this page permanently delete data and cannot be undone. Preview the count before proceeding.</p>
        </div>
    </div>

    {{-- ── CARD 1: Purge Audit Trail ── --}}
    <div class="bg-white border border-gray-200 rounded-xl shrink-0">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-scroll text-red-500 text-xs"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-800">Purge Audit Trail</h2>
                <p class="text-xs text-gray-400">Permanently delete asset audit trail entries from the system.</p>
            </div>
        </div>
        <div class="px-6 py-5 space-y-5">

            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Select Mode</label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach(['all' => ['label' => 'Purge All', 'desc' => 'Delete every audit entry'], 'date_range' => ['label' => 'Date Range', 'desc' => 'Delete within a range'], 'year' => ['label' => 'By Year', 'desc' => 'Delete a full year\'s entries']] as $val => $opt)
                    <label class="flex items-start gap-2.5 p-3 rounded-xl border-2 cursor-pointer transition-all"
                           :class="'{{ $val }}' === $wire.auditMode ? 'border-red-400 bg-red-50' : 'border-gray-200 hover:border-gray-300'">
                        <input type="radio" name="auditMode" value="{{ $val }}" wire:model.live="auditMode" class="mt-0.5 accent-red-500">
                        <span>
                            <span class="block text-sm font-semibold text-gray-700">{{ $opt['label'] }}</span>
                            <span class="block text-xs text-gray-400 mt-0.5">{{ $opt['desc'] }}</span>
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div x-show="$wire.auditMode === 'date_range'" x-cloak class="grid grid-cols-2 gap-4">
                <div class="input-group">
                    <label>From Date</label>
                    <input type="date" wire:model.live="auditDateFrom">
                </div>
                <div class="input-group">
                    <label>To Date</label>
                    <input type="date" wire:model.live="auditDateTo">
                </div>
            </div>

            <div x-show="$wire.auditMode === 'year'" x-cloak class="max-w-xs">
                <div class="input-group">
                    <label>Select Year</label>
                    <select wire:model.live="auditYear">
                        <option value="">— Select Year —</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <button type="button" wire:click="previewAudit"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fa-solid fa-eye text-gray-400 text-[10px]"></i> Preview Count
                </button>
                @if($auditCount !== null)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full bg-red-50 text-red-600 border border-red-100">
                    <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                    {{ number_format($auditCount) }} {{ Str::plural('entry', $auditCount) }} will be deleted
                </span>
                @endif
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center gap-4">
                @if($auditCount !== null)
                <button type="button" @click="openModal('audit', {{ $auditCount }})"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-xl transition-colors">
                    <i class="fa-solid fa-trash text-xs"></i> Purge Audit Trail
                </button>
                @else
                <button type="button" disabled
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-400 bg-gray-100 rounded-xl cursor-not-allowed">
                    <i class="fa-solid fa-trash text-xs"></i> Purge Audit Trail
                </button>
                @endif
                <p class="text-xs text-gray-400">Preview count first to enable this action.</p>
            </div>

        </div>
    </div>

    {{-- ── CARD 2: Purge Asset Attachments ── --}}
    <div class="bg-white border border-gray-200 rounded-xl shrink-0">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-paperclip text-orange-500 text-xs"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-800">Purge Asset Attachments</h2>
                <p class="text-xs text-gray-400">Delete attachment files from assets without removing the asset records.</p>
            </div>
        </div>
        <div class="px-6 py-5 space-y-5">

            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Select Mode</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-start gap-2.5 p-3 rounded-xl border-2 cursor-pointer transition-all"
                           :class="$wire.attachMode === 'quarter' ? 'border-orange-400 bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                        <input type="radio" name="attachMode" value="quarter" wire:model.live="attachMode" class="mt-0.5 accent-orange-500">
                        <span>
                            <span class="block text-sm font-semibold text-gray-700">By Quarter</span>
                            <span class="block text-xs text-gray-400 mt-0.5">Select a year and quarter (Q1–Q4)</span>
                        </span>
                    </label>
                    <label class="flex items-start gap-2.5 p-3 rounded-xl border-2 cursor-pointer transition-all"
                           :class="$wire.attachMode === 'date_range' ? 'border-orange-400 bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                        <input type="radio" name="attachMode" value="date_range" wire:model.live="attachMode" class="mt-0.5 accent-orange-500">
                        <span>
                            <span class="block text-sm font-semibold text-gray-700">Custom Range</span>
                            <span class="block text-xs text-gray-400 mt-0.5">Specify a custom date range</span>
                        </span>
                    </label>
                </div>
            </div>

            <div x-show="$wire.attachMode === 'quarter'" x-cloak class="grid grid-cols-2 gap-4">
                <div class="input-group">
                    <label>Year</label>
                    <select wire:model.live="attachYear">
                        <option value="">— Select Year —</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group">
                    <label>Quarter</label>
                    <select wire:model.live="attachQuarter" @if(!$attachYear) disabled @endif>
                        <option value="">— Select Quarter —</option>
                        <option value="Q1">Q1 — Jan–Mar</option>
                        <option value="Q2">Q2 — Apr–Jun</option>
                        <option value="Q3">Q3 — Jul–Sep</option>
                        <option value="Q4">Q4 — Oct–Dec</option>
                    </select>
                </div>
            </div>

            <div x-show="$wire.attachMode === 'date_range'" x-cloak class="grid grid-cols-2 gap-4">
                <div class="input-group">
                    <label>From Date</label>
                    <input type="date" wire:model.live="attachDateFrom">
                </div>
                <div class="input-group">
                    <label>To Date</label>
                    <input type="date" wire:model.live="attachDateTo">
                </div>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <button type="button" wire:click="previewAttach"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fa-solid fa-eye text-gray-400 text-[10px]"></i> Preview Count
                </button>
                @if($attachCount !== null)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full bg-orange-50 text-orange-600 border border-orange-100">
                    <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                    {{ number_format($attachCount) }} {{ Str::plural('asset', $attachCount) }} will have files removed
                </span>
                @endif
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center gap-4">
                @if($attachCount !== null)
                <button type="button" @click="openModal('attach', {{ $attachCount }})"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-orange-500 hover:bg-orange-600 rounded-xl transition-colors">
                    <i class="fa-solid fa-file-slash text-xs"></i> Purge Attachments
                </button>
                @else
                <button type="button" disabled
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-400 bg-gray-100 rounded-xl cursor-not-allowed">
                    <i class="fa-solid fa-file-slash text-xs"></i> Purge Attachments
                </button>
                @endif
                <p class="text-xs text-gray-400">Asset records are kept — only the files are deleted.</p>
            </div>

        </div>
    </div>

    {{-- ── CARD 3: Purge User Logs ── --}}
    <div class="bg-white border border-gray-200 rounded-xl shrink-0">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-clock-rotate-left text-red-500 text-xs"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-800">Purge User Logs</h2>
                <p class="text-xs text-gray-400">Permanently delete login/activity log entries from the system.</p>
            </div>
        </div>
        <div class="px-6 py-5 space-y-5">

            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Select Mode</label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach(['all' => ['label' => 'Purge All', 'desc' => 'Delete every log entry'], 'date_range' => ['label' => 'Date Range', 'desc' => 'Delete within a range'], 'year' => ['label' => 'By Year', 'desc' => 'Delete a full year\'s logs']] as $val => $opt)
                    <label class="flex items-start gap-2.5 p-3 rounded-xl border-2 cursor-pointer transition-all"
                           :class="'{{ $val }}' === $wire.logMode ? 'border-red-400 bg-red-50' : 'border-gray-200 hover:border-gray-300'">
                        <input type="radio" name="logMode" value="{{ $val }}" wire:model.live="logMode" class="mt-0.5 accent-red-500">
                        <span>
                            <span class="block text-sm font-semibold text-gray-700">{{ $opt['label'] }}</span>
                            <span class="block text-xs text-gray-400 mt-0.5">{{ $opt['desc'] }}</span>
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div x-show="$wire.logMode === 'date_range'" x-cloak class="grid grid-cols-2 gap-4">
                <div class="input-group">
                    <label>From Date</label>
                    <input type="date" wire:model.live="logDateFrom">
                </div>
                <div class="input-group">
                    <label>To Date</label>
                    <input type="date" wire:model.live="logDateTo">
                </div>
            </div>

            <div x-show="$wire.logMode === 'year'" x-cloak class="max-w-xs">
                <div class="input-group">
                    <label>Select Year</label>
                    <select wire:model.live="logYear">
                        <option value="">— Select Year —</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <button type="button" wire:click="previewLog"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fa-solid fa-eye text-gray-400 text-[10px]"></i> Preview Count
                </button>
                @if($logCount !== null)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full bg-red-50 text-red-600 border border-red-100">
                    <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                    {{ number_format($logCount) }} {{ Str::plural('entry', $logCount) }} will be deleted
                </span>
                @endif
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center gap-4">
                @if($logCount !== null)
                <button type="button" @click="openModal('log', {{ $logCount }})"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-xl transition-colors">
                    <i class="fa-solid fa-trash text-xs"></i> Purge User Logs
                </button>
                @else
                <button type="button" disabled
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-400 bg-gray-100 rounded-xl cursor-not-allowed">
                    <i class="fa-solid fa-trash text-xs"></i> Purge User Logs
                </button>
                @endif
                <p class="text-xs text-gray-400">Preview count first to enable this action.</p>
            </div>

        </div>
    </div>

    {{-- ── Confirmation Modal ── --}}
    <div x-show="showModal"
         x-transition.opacity
         class="fixed inset-0 bg-black/40 z-[70] flex items-center justify-center px-4"
         @click.self="showModal = false">

        <div x-show="showModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">

            <div class="flex flex-col items-center text-center gap-4">
                <div class="w-14 h-14 rounded-full flex items-center justify-center"
                     :class="modal.section === 'attach' ? 'bg-orange-50' : 'bg-red-50'">
                    <i class="fa-solid fa-triangle-exclamation text-xl"
                       :class="modal.section === 'attach' ? 'text-orange-500' : 'text-red-500'"></i>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-gray-800">Confirm Permanent Deletion</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        This will permanently delete
                        <strong :class="modal.section === 'attach' ? 'text-orange-600' : 'text-red-600'"
                                x-text="modal.count.toLocaleString()"></strong>
                        <span x-text="modal.section === 'attach' ? ' attachment file(s). Asset records will be kept.' : ' records. This action cannot be undone.'"></span>
                    </p>
                </div>

                <div class="w-full text-left">
                    <label class="block text-xs font-semibold text-gray-500 mb-2">
                        Type <strong x-text="modal.count" :class="modal.section === 'attach' ? 'text-orange-600' : 'text-red-600'"></strong> to confirm
                    </label>
                    <input type="text"
                           x-ref="confirmInput"
                           x-model="confirmInput"
                           placeholder="Enter the number shown above"
                           @keydown.enter="doConfirm()"
                           autocomplete="off"
                           class="w-full px-3 py-2 text-sm border rounded-xl outline-none transition-colors"
                           :class="canConfirm
                               ? (modal.section === 'attach' ? 'border-orange-400 ring-1 ring-orange-300' : 'border-red-400 ring-1 ring-red-300')
                               : 'border-gray-200 focus:border-gray-400'">
                </div>

                <div class="flex gap-3 w-full">
                    <button type="button" @click="showModal = false; confirmInput = ''"
                        class="flex-1 px-4 py-2 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="button"
                        :disabled="!canConfirm"
                        @click="doConfirm()"
                        class="flex-1 px-4 py-2 text-sm font-bold text-white rounded-xl transition-colors"
                        :class="canConfirm
                            ? (modal.section === 'attach' ? 'bg-orange-500 hover:bg-orange-600 cursor-pointer' : 'bg-red-500 hover:bg-red-600 cursor-pointer')
                            : 'bg-gray-200 text-gray-400 cursor-not-allowed'">
                        Confirm & Delete
                    </button>
                </div>
            </div>

        </div>
    </div>

</div>

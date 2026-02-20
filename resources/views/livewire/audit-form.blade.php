<div class="relative overflow-y-auto flex flex-col gap-7"
    x-data="{
        showModal: @entangle('showConfirmModal'),
        modalTemplate: '',
    }"    
>
    <div class="card self-center relative max-w-4xl">
    
        <i class="fa-solid fa-arrow-left absolute top-8 -left-[50px] cursor-pointer hover:-translate-x-1 text-gray-400 hover:text-gray-800 text-xl" onclick="window.history.back()"></i>
        
        <h1 class="text-lg font-bold">Audit Asset</h1>
        <p class="text-gray-400 text-sm mb-7">Review asset details and record audit findings.</p>

        <!-- ASSET INFORMATION (READ-ONLY) - COMPACT GRID -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 mb-7">
            <h2 class="text-sm font-bold text-gray-700 mb-4 uppercase">Asset Information</h2>
            
            <div class="grid grid-cols-2 gap-x-8 gap-y-3 text-sm">
                <div class="flex">
                    <span class="text-gray-500 font-medium w-32">Category:</span>
                    <span class="text-gray-800 font-semibold">{{$category}}</span>
                </div>
                
                <div class="flex">
                    <span class="text-gray-500 font-medium w-32">Sub-category:</span>
                    <span class="text-gray-800 font-semibold">{{$sub_category}}</span>
                </div>
                
                <div class="flex">
                    <span class="text-gray-500 font-medium w-32">Brand:</span>
                    <span class="text-gray-800 font-semibold">{{$brand}}</span>
                </div>
                
                <div class="flex">
                    <span class="text-gray-500 font-medium w-32">Model:</span>
                    <span class="text-gray-800 font-semibold">{{$model}}</span>
                </div>
                
                <div class="flex">
                    <span class="text-gray-500 font-medium w-32">Farm:</span>
                    <span class="text-gray-800 font-semibold">{{$farm}}</span>
                </div>
                
                <div class="flex">
                    <span class="text-gray-500 font-medium w-32">Location:</span>
                    <span class="text-gray-800 font-semibold">{{$location}}</span>
                </div>
                
                <div class="flex col-span-2">
                    <span class="text-gray-500 font-medium w-32">Description:</span>
                    <span class="text-gray-800">{{$description}}</span>
                </div>

                <div class="flex col-span-2">
                    <span class="text-gray-500 font-medium w-32">Last Audit:</span>
                    <span class="text-gray-800">
                        @if($last_audit)
                            {{$last_audit->audited_at->format('M d, Y')}} by <b class="underline">{{$last_audit->audited_by_name}}</b>
                        @else 
                            <span class="text-gray-400 italic">No previous audit</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- AUDIT ENTRY (EDITABLE) -->
        <div class="flex flex-col gap-5">
            <h2 class="text-sm font-bold text-gray-700 uppercase">Record Audit</h2>

            <div class="input-group">
                <label for="next_audit" class="font-semibold">Next Audit Date: @error('next_audit')<span class="text-red-500 text-xs ml-2">Required</span>@enderror</label>
                <input 
                    type="date" 
                    id="next_audit" 
                    class="{{ $errors->has('next_audit') ? '!border-red-400' : '' }}" 
                    wire:model="next_audit"
                >
            </div>

            <div class="input-group">
                <label for="notes" class="font-semibold">Audit Notes:</label>
                <textarea 
                    id="notes" 
                    wire:model="notes" 
                    rows="5" 
                    placeholder="Document findings, condition changes, or maintenance requirements..."
                    class="resize-none"
                ></textarea>
            </div>

            <div class="file-group flex flex-col gap-2">
                <label class="font-semibold">
                    Supporting Documents:
                    @error('attachment')
                        <span class="text-red-500 text-xs ml-2">{{ $message }}</span>
                    @enderror
                </label>

                <div class="flex w-full border border-gray-400 rounded-md overflow-hidden text-sm">
                    <div 
                        class="bg-gray-600 text-white px-4 py-2 cursor-pointer hover:bg-gray-500 transition"
                        @click="$refs.attachment.click()"
                    >
                        <i class="fa-solid fa-upload mr-2"></i>Choose File
                    </div>

                    <div class="flex-1 bg-gray-50 text-gray-500 px-4 py-2 flex items-center">
                        {{ $attachment ? $attachment->getClientOriginalName() : 'No file selected' }}
                    </div>

                    <input 
                        x-ref="attachment"
                        type="file"
                        class="hidden"
                        wire:model="attachment"
                        accept="application/pdf,image/jpeg,image/jpg,image/png"
                    >
                </div>
                <p class="text-xs text-gray-400">PDF, JPG, PNG (Max 10MB)</p>
            </div>

            <div class="flex justify-end gap-3 mt-3 pt-5 border-t">
                <button 
                    type="button"
                    onclick="window.history.back()"
                    class="px-5 py-3 border-2 border-gray-300 rounded-lg font-bold text-gray-600 text-xs hover:bg-gray-100"
                >
                    CANCEL
                </button>
                <button 
                    type="button"
                    @click="modalTemplate = 'submit', $wire.trySubmit()"
                    class="px-5 py-3 bg-[#4fd1c5] rounded-lg font-bold text-white text-xs hover:bg-teal-500"
                >
                    <i class="fa-solid fa-check mr-2"></i>SUBMIT AUDIT
                </button>
            </div>
        </div>
    </div>

    <!-- Backdrop -->
    <div 
        x-show="showModal"
        x-transition.opacity
        class="fixed inset-0 bg-black/30 z-40"
    ></div>

    <!-- Modal -->
    <div 
        x-show="showModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="fixed inset-0 flex items-center justify-center z-50"
    >
        <div class="relative bg-white p-10 rounded-lg shadow-lg">
            <div class="absolute right-7 top-7 text-gray-400 cursor-pointer hover:text-gray-800" @click="showModal = false"><i class="fa-solid fa-xmark"></i></div>
            
            <!-- SUBMIT MODAL -->
            <div class="flex flex-col gap-5 w-[23rem]" x-show="modalTemplate === 'submit'">
                <h2 class="text-xl font-semibold -mb-2">Submit Audit</h2>
                <p>Confirm audit submission for this asset. Audit details will be recorded and added to the asset's audit history.</p>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="showModal = false;" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100 cursor-pointer">Cancel</button>
                    <button type="button" @click="showModal = false; $wire.submit()" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-800 cursor-pointer">Confirm</button>
                </div>
            </div>
            
        </div>

    </div>
</div>
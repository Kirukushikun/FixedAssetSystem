<div class="relative overflow-y-auto flex flex-col gap-7"
    x-data="{
        showModal: @entangle('showConfirmModal'),
        modalTemplate: '',
    }"    
>
    <div class="card self-center relative">
    
        <i class="fa-solid fa-arrow-left absolute top-8 -left-[50px] cursor-pointer hover:-translate-x-1 text-gray-400 hover:text-gray-800 text-xl" onclick="window.history.back()"></i>
        <h1 class="text-lg font-bold">Audit Information</h1>
        <p class="text-gray-400 text-sm mb-7">Basic details that describe and identify this asset. These values help classify and track the item within the system.</p>

        <div class="flex flex-col gap-5">
            <div class="input-group">
                <label for="ref_id">Model: </label>
                <div class="">{{$targetAsset->brand}} {{$targetAsset->model}}</div>
            </div>

            <div class="input-group">
                <label for="ref_id">Location: </label>
                <!-- Add wire:model to your inputs -->
                <select wire:model="location" class="{{ $errors->has('location') ? '!border-red-400' : '' }}">
                    <option value="">Select Location</option>
                    <option value="BFC">BFC</option>
                    <option value="BDL">BDL</option>
                    <option value="PFC">PFC</option>
                    <option value="RH">RH</option>
                </select>
                <p class="text-xs text-gray-400">This helps ensure accurate inventory tracking during audit checks.</p>
            </div>

            <div class="input-group">
                <label for="ref_id">Last Audit: </label>
                <div >
                    @if($last_audit)
                        {{$last_audit->audited_at->format('d/m/Y')}}
                    @else 
                        No previous audit
                    @endif
                </div>
            </div>

            <div class="input-group">
                <label for="ref_id">Next Audit: </label>
                <input type="date" class="{{ $errors->has('next_audit') ? '!border-red-400' : '' }}" wire:model="next_audit">
                <p class="text-xs text-gray-400">Used to schedule routine reviews and ensure this asset is inspected regularly.</p>
            </div>

            <div class="input-group">
                <label for="ref_id">Notes: </label>
                <textarea wire:model="notes"></textarea>
                <p class="text-xs text-gray-400">Use this to describe issues, observations, or special conditions noticed during inspection.</p>
            </div>

            <div class="file-group flex flex-col gap-2">

                <div class="file-group flex flex-col gap-2">
                    <label for="attachment" class="text-[15px] font-semibold relative">
                        Attachment(s):
                        @error('attachment')
                            <span class="absolute bg-white text-red-600 right-0 bottom-[-20px] text-xs p-1">
                                {{ $message }}
                            </span>
                        @enderror
                    </label>

                    <!-- Same layout container -->
                    <div class="flex w-full border border-gray-400 rounded-md overflow-hidden text-sm relative">

                        <!-- Clickable Upload Button -->
                        <div 
                            class="bg-gray-600 text-white px-4 py-2 cursor-pointer hover:bg-gray-500"
                            @click="$refs.attachment.click()"
                        >
                            Upload File
                        </div>

                        <!-- Filename or placeholder -->
                        <div class="flex-1 bg-gray-50 text-gray-500 px-4 py-2">
                            {{ $attachment ? $attachment->getClientOriginalName() : 'No file attached' }}
                        </div>

                        <!-- Hidden Real Input -->
                        <input 
                            x-ref="attachment"
                            type="file"
                            class="hidden"
                            wire:model="attachment"
                            accept="application/pdf"
                        >
                    </div>
                </div>

                <p class="text-xs text-gray-400">Upload supporting images or documents for this audit entry.</p>
            </div>

            <div class="self-end flex gap-3">
                <button 
                    type="button"
                    @click="modalTemplate = 'submit', $wire.trySubmit()"
                    class="px-5 py-3 bg-[#4fd1c5] rounded-lg font-bold text-white text-xs hover:bg-teal-500"
                >
                    AUDIT
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
                <p>Confirm audit submission for this asset. Audit details will be recorded and added to the asset’s audit history.</p>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="showModal = false;" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100 cursor-pointer">Cancel</button>
                    <button type="button" @click="showModal = false; $wire.submit()" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-800 cursor-pointer">Confirm</button>
                </div>
            </div>
            
        </div>

    </div>
</div>

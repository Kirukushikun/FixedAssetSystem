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
                <div class="">HP-0123-94</div>
                
            </div>

            <div class="input-group">
                <label for="ref_id">Location: </label>
                <select name="" id="">
                    <option value=""></option>
                </select>
                <p class="text-xs text-gray-400">This helps ensure accurate inventory tracking during audit checks.</p>
            </div>

            <div class="input-group">
                <label for="ref_id">Last Audit: </label>
                <div class="">
                    None
                </div>
            </div>

            <div class="input-group">
                <label for="ref_id">Next Audit: </label>
                <input type="date" w readonly>
                <p class="text-xs text-gray-400">Used to schedule routine reviews and ensure this asset is inspected regularly.</p>
            </div>

            <div class="input-group">
                <label for="ref_id">Notes: </label>
                <textarea type="text"></textarea>
                <p class="text-xs text-gray-400">Use this to describe issues, observations, or special conditions noticed during inspection.</p>
            </div>

            <div class="file-group flex flex-col gap-2">
                <label class="text-[15px] font-semibold">Attachment(s):</label>

                <div class="flex w-full border border-gray-400 rounded-md overflow-hidden text-sm">
                    <div class="bg-gray-600 text-white px-4 py-2 cursor-pointer hover:bg-gray-500" disabled>
                        View File
                    </div>
                    
                    <div class="flex-1 bg-gray-50 text-gray-500 px-4 py-2">
                        No file attached
                    </div>
                </div>

                <p class="text-xs text-gray-400">Upload supporting images or documents for this audit entry.</p>
            </div>

            <div class="self-end flex gap-3">
                <button class="px-5 py-3 bg-[#4fd1c5] rounded-lg font-bold text-white text-xs hover:bg-teal-500">AUDIT</button>
            </div>
        </div>
    </div>
</div>

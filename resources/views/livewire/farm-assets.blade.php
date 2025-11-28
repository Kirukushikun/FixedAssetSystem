<div 
    class="flex flex-col gap-5 rounded-md relative h-[70vh] max-h-[70vh]" x-show="modalTemplate === 'farm-assets'"
>

    <h2 class="text-xl font-semibold">Farm Assets ({{$farmCode}})</h2>

    <!-- Scrollable table wrapper -->
    <div class="overflow-auto flex-1">
        <table class="w-full border-collapse border border-gray-300 text-sm">
            <thead class="bg-gray-100 sticky top-0 z-10">
                <tr>
                    <th class="border border-gray-300 px-2 py-1 text-left">REFERENCE ID</th>
                    <th class="border border-gray-300 px-2 py-1 text-left">CATEGORY</th>
                    <th class="border border-gray-300 px-2 py-1 text-left">SUB-CATEGORY</th>
                    <th class="border border-gray-300 px-2 py-1 text-left">BRAND</th>
                    <th class="border border-gray-300 px-2 py-1 text-left">MODEL</th>
                    <th class="border border-gray-300 px-2 py-1 text-left">STATUS</th>
                    <th class="border border-gray-300 px-2 py-1 text-left">CONDITION</th>
                    <th class="border border-gray-300 px-2 py-1 text-left">ASSIGNED TO</th>
                    <th class="border border-gray-300 px-2 py-1 text-left">ACTION</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assets as $asset)
                    <tr>
                        <td class="border border-gray-300 px-2 py-1">{{$asset->ref_id}}</td>
                        @php
                            $categoryValue = [
                                'it' => 'IT Equipment',
                                'office' => 'Office Furniture',
                                'appliances' => 'Appliances',
                                'audio' => 'Audio Equipment',
                                'tools' => 'Tools & Misc',
                                'kitchen' => 'Kitchen Equipment'
                            ]
                        @endphp
                        <td class="border border-gray-300 px-2 py-1">{{$categoryValue[$asset->category]}}</td>
                        <td class="border border-gray-300 px-2 py-1">{{$asset->sub_category}}</td>
                        <td class="border border-gray-300 px-2 py-1">{{$asset->brand}}</td>
                        <td class="border border-gray-300 px-2 py-1">{{$asset->model}}</td>
                        <td class="border border-gray-300 px-2 py-1">{{$asset->status}}</td>
                        <td class="border border-gray-300 px-2 py-1">{{$asset->condition}}</td>
                        <td class="border border-gray-300 px-2 py-1">{{$asset->assigned_name}}</td>
                        <td class="border border-gray-300 px-2 py-1">
                            <button class="bg-teal-400 text-white rounded-md text-xs w-fit py-2 px-4 hover:bg-teal-500 transition">
                                VIEW
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="border border-gray-300 px-2 py-1 text-center">No assets found for this farm.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Placeholder -->
    <div class="absolute bottom-0 left-0 w-full bg-white py-3 flex justify-center">
        <div class="text-sm text-gray-600">
            Pagination Placeholder
        </div>
    </div>

</div>
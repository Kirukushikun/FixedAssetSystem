<div class="table-container flex-1 flex flex-col min-h-0 overflow-y-auto flex-1">
    <table class="w-full">
        <thead>
            <tr>
                <th>Ref ID</th>
                <th>Deleted At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($deletedAssets as $item)
            <tr>
                <td>{{$item->ref_id}}</td>
                <td>{{$item->updated_at->format('m/d/Y')}}</td>
                <td>
                    <button 
                        class="bg-white border border-2 text-gray-500 rounded-md text-xs py-2 px-4 hover:bg-gray-500 hover:text-white transition"
                    >
                        RESTORE
                    </button>
                </td>
            </tr>
            @endforeach            
        </tbody>
    </table>
</div>
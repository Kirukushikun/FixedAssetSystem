<div class="table-container tab-content h-full flex flex-col min-h-0 hidden" id="access">
    <div class="overflow-x-auto flex-1 min-h-0">
        <table class="min-w-max">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>EMAIL</th>
                    <th>NAME</th>
                    <th>ACCESS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{$user['id']}}</td>
                    <td>{{$user['first_name']}} {{$user['last_name']}}</td>
                    <td>{{$user['email']}}</td>
                    <td>
                        <button class="py-2 px-3 rounded-md border border-green-600 text-green-600 hover:bg-green-600 hover:text-white">
                            Grant
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
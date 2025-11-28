
<div class="table-container flex-1 flex flex-col min-h-0 overflow-y-auto flex-1">
    <table class="w-full">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr class="border-b border-gray-200">
                    <td>{{ $user['id'] }}</td>
                    <td>
                        {{ $user['first_name'] }} {{ $user['last_name'] }}
                    </td>
                    <td>{{ $user['email'] }}</td>
                    <td>
                        @if($dbUsers->has($user['id']))
                            <button 
                                wire:click="revokeAccess('{{ $user['id'] }}', '{{ $user['first_name'] }} {{ $user['last_name'] }}')"
                                wire:confirm="Are you sure you want to revoke access for this user?"
                                class="bg-red-500 text-white rounded-md text-xs py-2 px-4 hover:bg-red-600 transition"
                            >
                                REVOKE ACCESS
                            </button>
                        @else
                            <button 
                                wire:click="grantAccess('{{ $user['id'] }}', '{{ $user['first_name'] }} {{ $user['last_name'] }}', '{{ $user['email'] }}')"
                                class="bg-teal-400 text-white rounded-md text-xs py-2 px-4 hover:bg-teal-500 transition"
                            >
                                GRANT ACCESS
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                        No users found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

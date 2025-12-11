<div class="table-container flex-1 flex flex-col min-h-0 overflow-y-auto flex-1" x-data="{ showModal: false, modalTemplate: '' }">
    <table class="w-full">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Farm</th>
                <th>Department</th>
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
                            {{ $dbUsers->get($user['id'])->farm ?? '-' }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($dbUsers->has($user['id']))
                            {{ $dbUsers->get($user['id'])->department ?? '-' }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($dbUsers->has($user['id']))
                            <div class="flex gap-2">
                                <button 
                                    @click="modalTemplate = 'edit'; showModal = true; $wire.openEditModal('{{ $user['id'] }}');"
                                    class="bg-blue-500 text-white rounded-md text-xs py-2 px-4 hover:bg-blue-600 transition"
                                >
                                    EDIT
                                </button>
                                <button 
                                    @click="modalTemplate = 'revoke'; showModal = true; $wire.set('selectedUserId', '{{ $user['id'] }}'); $wire.set('selectedUserName', '{{ $user['first_name'] }} {{ $user['last_name'] }}');"
                                    class="bg-red-500 text-white rounded-md text-xs py-2 px-4 hover:bg-red-600 transition"
                                >
                                    REVOKE ACCESS
                                </button>
                            </div>
                        @else
                            <button 
                                @click="modalTemplate = 'grant'; showModal = true; $wire.set('selectedUserId', '{{ $user['id'] }}'); $wire.set('selectedUserName', '{{ $user['first_name'] }} {{ $user['last_name'] }}'); $wire.set('selectedUserEmail', '{{ $user['email'] }}');"
                                class="bg-teal-400 text-white rounded-md text-xs py-2 px-4 hover:bg-teal-500 transition"
                            >
                                GRANT ACCESS
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                        No users found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Backdrop -->
    <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-black/30 z-40"></div>

    <!-- Modal Container -->
    <div
        x-show="showModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="fixed inset-0 flex items-center justify-center z-50"
        @open-edit-modal.window="showModal = true; modalTemplate = 'edit'"
    >
        <div class="relative bg-white p-8 rounded-lg shadow-lg w-[26rem]">
            <button class="absolute right-7 top-7 text-gray-400 hover:text-gray-800" @click="showModal = false">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <!-- Grant Access Modal -->
            <div class="flex flex-col gap-5" x-show="modalTemplate === 'grant'">
                <h2 class="text-xl font-semibold -mb-2">Grant Access</h2>
                <p>Are you sure you want to grant access for <strong>{{ $selectedUserName ?? '' }}</strong>?</p>

                <div class="flex justify-end gap-3">
                    <button @click="showModal = false" class="px-4 py-2 border rounded hover:bg-gray-100">Cancel</button>
                    <button 
                        @click="$wire.confirmGrantAccess(); showModal = false" 
                        class="px-4 py-2 bg-teal-400 text-white rounded hover:bg-teal-500"
                    >
                        Confirm
                    </button>
                </div>
            </div>

            <!-- Revoke Access Modal -->
            <div class="flex flex-col gap-5" x-show="modalTemplate === 'revoke'">
                <h2 class="text-xl font-semibold -mb-2">Revoke Access</h2>
                <p>Are you sure you want to revoke access for <strong>{{ $selectedUserName ?? '' }}</strong>?</p>

                <div class="flex justify-end gap-3">
                    <button @click="showModal = false" class="px-4 py-2 border rounded hover:bg-gray-100">Cancel</button>
                    <button 
                        @click="$wire.confirmRevokeAccess(); showModal = false" 
                        class="px-4 py-2 bg-red-700 text-white rounded hover:bg-red-800"
                    >
                        Confirm
                    </button>
                </div>
            </div>

            <!-- Edit Modal -->
            <div class="flex flex-col gap-5" x-show="modalTemplate === 'edit'">
                <h2 class="text-xl font-semibold -mb-2">Edit User Details</h2>
                
                <div class="flex flex-col gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Farm</label>
                        <select 
                            wire:model="editFarm"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-400"
                        >
                            <option value="BFC">BFC</option>
                            <option value="BDL">BDL</option>
                            <option value="PFC">PFC</option>
                            <option value="RH">RH</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <select 
                            wire:model="editDepartment"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-400"
                        >
                            @foreach($departments as $department)
                                <option value="{{ $department->name }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button @click="showModal = false" class="px-4 py-2 border rounded hover:bg-gray-100">Cancel</button>
                    <button 
                        @click="$wire.updateUserDetails(); showModal = false" 
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                    >
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
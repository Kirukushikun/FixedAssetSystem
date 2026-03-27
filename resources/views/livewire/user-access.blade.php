<div class="h-full flex flex-col gap-4"
     x-data="{ showModal: false, modalTemplate: '' }"
     @open-edit-modal.window="showModal = true; modalTemplate = 'edit'"
     @keydown.escape.window="showModal = false; modalTemplate = ''">

    {{-- ── Toolbar ── --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-lg font-bold text-[#2d3748]">User Access</h1>

        <div class="flex items-center gap-2">
            <div class="flex items-center gap-2 border border-gray-200 rounded-lg px-3 py-2 bg-white hover:border-teal-400 transition-colors">
                <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                <input
                    class="outline-none text-sm bg-transparent w-40 placeholder-gray-400"
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search users..."
                >
            </div>
        </div>
    </div>


    {{-- ── Table ── --}}
    <div class="table-container flex-1 flex flex-col min-h-0">
        <div class="flex-1 overflow-y-auto overflow-x-auto minimal-scroll">

            @if($users->isEmpty())
                <div class="flex flex-col items-center justify-center h-full gap-3 text-gray-400 py-24">
                    <i class="fa-solid fa-users text-4xl"></i>
                    <p class="text-sm font-semibold">No users found</p>
                    <p class="text-xs">Could not retrieve users from the system</p>
                </div>
            @else
                <table class="w-full border-collapse">
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
                        @foreach($users as $user)
                        <tr>
                            <td class="font-mono text-xs text-gray-500">{{ $user['id'] }}</td>
                            <td class="text-sm font-semibold">
                                @if($dbUsers->has($user['id']) && $dbUsers->get($user['id'])->is_admin)
                                    <i class="fa-solid fa-user-tie text-yellow-500 mr-1" title="Admin"></i>
                                @endif
                                {{ $user['first_name'] }} {{ $user['last_name'] }}
                            </td>
                            <td class="text-sm text-gray-600">{{ $user['email'] }}</td>
                            <td class="text-sm text-gray-600">
                                {{ $dbUsers->has($user['id']) ? ($dbUsers->get($user['id'])->farm ?? '-') : '-' }}
                            </td>
                            <td class="text-sm text-gray-600">
                                {{ $dbUsers->has($user['id']) ? ($dbUsers->get($user['id'])->department ?? '-') : '-' }}
                            </td>
                            <td>
                                @if($dbUsers->has($user['id']))
                                    <div class="flex items-center gap-2">
                                        {{-- Edit --}}
                                        <button
                                            @click="modalTemplate = 'edit'; showModal = true; $wire.openEditModal('{{ $user['id'] }}');"
                                            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-500 border border-blue-200 rounded-lg hover:bg-blue-50 transition-colors"
                                        >
                                            <i class="fa-solid fa-pen text-xs"></i> Edit
                                        </button>

                                        {{-- Revoke --}}
                                        <button
                                            @click="modalTemplate = 'revoke'; showModal = true; $wire.set('selectedUserId', '{{ $user['id'] }}'); $wire.set('selectedUserName', '{{ $user['first_name'] }} {{ $user['last_name'] }}');"
                                            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-red-500 border border-red-200 rounded-lg hover:bg-red-50 transition-colors"
                                        >
                                            <i class="fa-solid fa-ban text-xs"></i> Revoke
                                        </button>

                                        {{-- Make Admin --}}
                                        @if(!$dbUsers->get($user['id'])->is_admin)
                                            <button
                                                @click="modalTemplate = 'admin'; showModal = true; $wire.set('selectedUserId', '{{ $user['id'] }}'); $wire.set('selectedUserName', '{{ $user['first_name'] }} {{ $user['last_name'] }}');"
                                                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-yellow-600 border border-yellow-200 rounded-lg hover:bg-yellow-50 transition-colors"
                                            >
                                                <i class="fa-solid fa-user-tie text-xs"></i> Make Admin
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <button
                                        @click="modalTemplate = 'grant'; showModal = true; $wire.set('selectedUserId', '{{ $user['id'] }}'); $wire.set('selectedUserName', '{{ $user['first_name'] }} {{ $user['last_name'] }}'); $wire.set('selectedUserEmail', '{{ $user['email'] }}');"
                                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-teal-600 border border-teal-200 rounded-lg hover:bg-teal-50 transition-colors"
                                    >
                                        <i class="fa-solid fa-key text-xs"></i> Grant Access
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

        </div>

        {{-- ── Pagination ── --}}
        @php
            $currentPage = $users->currentPage();
            $lastPage    = $users->lastPage();

            $start = max(1, $currentPage - 2);
            $end   = min($lastPage, $start + 4);

            if ($end - $start < 4) {
                $start = max(1, $end - 4);
            }

            $pages = range($start, $end);
        @endphp

        @if($lastPage > 1)
            <div class="pagination-container flex items-center justify-end gap-3 mt-auto pt-4">
                
                <div class="text-xs text-gray-400">
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
                </div>

                {{-- Previous --}}
                @if($currentPage > 1)
                    <button
                        wire:click="previousPage"
                        class="px-2 py-2 rounded-md bg-teal-100 hover:scale-110 cursor-pointer text-sm">
                        <i class="fa-solid fa-caret-left text-teal-500"></i>
                    </button>
                @endif

                {{-- Page Numbers --}}
                @foreach($pages as $i)
                    <button
                        wire:click="gotoPage({{ $i }})"
                        class="px-4 py-2 rounded-md text-sm hover:scale-110 cursor-pointer
                            {{ $i == $currentPage ? 'bg-teal-400 text-white' : 'bg-teal-100 text-teal-500' }}">
                        {{ $i }}
                    </button>
                @endforeach

                {{-- Next --}}
                @if($currentPage < $lastPage)
                    <button
                        wire:click="nextPage"
                        class="px-2 py-2 rounded-md bg-teal-100 hover:scale-110 cursor-pointer text-sm">
                        <i class="fa-solid fa-caret-right text-teal-500"></i>
                    </button>
                @endif

            </div>
        @endif
    </div>

    {{-- ── Backdrop ── --}}
    <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-black/40 z-[70]" @click="showModal = false; modalTemplate = ''"></div>

    {{-- ── Modal Panel ── --}}
    <div
        x-show="showModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-[80] flex items-center justify-center px-4 pointer-events-none"
    >
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg pointer-events-auto">

            {{-- Close --}}
            <button
                class="absolute right-5 top-5 w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-colors z-10"
                @click="showModal = false; modalTemplate = ''"
            >
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            {{-- Grant Access --}}
            <div class="p-8" x-show="modalTemplate === 'grant'">
                <div class="flex flex-col items-center gap-4 text-center">
                    <div class="w-12 h-12 rounded-full bg-teal-100 flex items-center justify-center">
                        <i class="fa-solid fa-key text-teal-500"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 mb-1">Grant Access</h2>
                        <p class="text-sm text-gray-500">Are you sure you want to grant access to <strong class="text-gray-800">{{ $selectedUserName ?? '' }}</strong>?</p>
                    </div>
                    <div class="flex gap-3 w-full mt-2">
                        <button @click="showModal = false" class="flex-1 px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button @click="$wire.confirmGrantAccess(); showModal = false" class="flex-1 px-4 py-2 bg-teal-500 text-white rounded-xl text-sm font-semibold hover:bg-teal-600 transition-colors">
                            Grant Access
                        </button>
                    </div>
                </div>
            </div>

            {{-- Revoke Access --}}
            <div class="p-8" x-show="modalTemplate === 'revoke'">
                <div class="flex flex-col items-center gap-4 text-center">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <i class="fa-solid fa-ban text-red-500"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 mb-1">Revoke Access</h2>
                        <p class="text-sm text-gray-500">Are you sure you want to revoke access for <strong class="text-gray-800">{{ $selectedUserName ?? '' }}</strong>?</p>
                    </div>
                    <div class="flex gap-3 w-full mt-2">
                        <button @click="showModal = false" class="flex-1 px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button @click="$wire.confirmRevokeAccess(); showModal = false" class="flex-1 px-4 py-2 bg-red-500 text-white rounded-xl text-sm font-semibold hover:bg-red-600 transition-colors">
                            Revoke Access
                        </button>
                    </div>
                </div>
            </div>

            {{-- Make Admin --}}
            <div class="p-8" x-show="modalTemplate === 'admin'">
                <div class="flex flex-col items-center gap-4 text-center">
                    <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center">
                        <i class="fa-solid fa-user-tie text-yellow-500"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 mb-1">Make Admin</h2>
                        <p class="text-sm text-gray-500">Are you sure you want to grant admin privileges to <strong class="text-gray-800">{{ $selectedUserName ?? '' }}</strong>?</p>
                    </div>
                    <div class="flex gap-3 w-full mt-2">
                        <button @click="showModal = false" class="flex-1 px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button @click="$wire.confirmMakeAdmin(); showModal = false" class="flex-1 px-4 py-2 bg-yellow-500 text-white rounded-xl text-sm font-semibold hover:bg-yellow-600 transition-colors">
                            Make Admin
                        </button>
                    </div>
                </div>
            </div>

            {{-- Edit User Details --}}
            <div class="p-8" x-show="modalTemplate === 'edit'">
                <h2 class="text-lg font-bold text-gray-800 mb-6">Edit User Details</h2>

                <div class="space-y-4">
                    <div class="input-group">
                        <label>Farm</label>
                        <select wire:model="editFarm">
                            <option value="BFC">BFC</option>
                            <option value="BDL">BDL</option>
                            <option value="PFC">PFC</option>
                            <option value="RH">RH</option>
                            <option value="BBGC">BBGC</option>
                            <option value="Hatchery">Hatchery</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Department</label>
                        <select wire:model="editDepartment">
                            @foreach($departments as $department)
                                <option value="{{ $department->name }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button @click="showModal = false" class="px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button @click="$wire.updateUserDetails(); showModal = false" class="px-4 py-2 bg-teal-500 text-white rounded-xl text-sm font-bold hover:bg-teal-600 transition-colors">
                        Save Changes
                    </button>
                </div>
            </div>

        </div>
    </div>

</div>
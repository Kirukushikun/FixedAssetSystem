<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Department;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserAccess extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $dbUsers = [];
    public $departments = [];
    public string $search = '';

    // Selected user properties for modal
    public $selectedUserId;
    public $selectedUserName;
    public $selectedUserEmail;

    // Edit properties
    public $editUserId;
    public $editFarm;
    public $editDepartment;

    public function mount()
    {
        $this->dbUsers = Cache::remember('user_access_db_users', 600, function () {
            return User::all()->keyBy('id');
        });

        $this->departments = Department::all();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    private function fetchUsers(): Collection
    {
        $users = Cache::remember('user_access_api_users', 300, function () {
            $response = Http::withHeaders([
                'x-api-key' => '123456789bgc'
            ])
            ->withOptions([
                'verify' => storage_path('cacert.pem'),
            ])
            ->post('https://bfcgroup.ph/api/v1/users');

            if ($response->successful()) {
                $json = $response->json();
                $users = $json['data'] ?? $json;

                return array_map(function ($user) {
                    try {
                        $user['id'] = Crypt::decryptString($user['id']);
                    } catch (\Exception $e) {
                        Log::error('Failed to decrypt user ID for: ' . $user['first_name'] . ' ' . $user['last_name']);
                    }
                    return $user;
                }, $users);
            }

            session()->flash('error', 'Failed to fetch users. Status: ' . $response->status());
            Log::error('API Error: ' . $response->status());
            return [];
        });

        return collect($users);
    }

    private function paginateUsers(): LengthAwarePaginator
    {
        $users = $this->fetchUsers();

        // Apply search filter on the collection
        if ($this->search) {
            $search = strtolower($this->search);
            $users = $users->filter(fn($user) =>
                str_contains(strtolower($user['first_name'] . ' ' . $user['last_name']), $search) ||
                str_contains(strtolower($user['email']), $search) ||
                str_contains(strtolower((string) $user['id']), $search)
            );
        }

        $perPage = 10;
        $page = $this->getPage();
        $total = $users->count();

        $items = $users->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );
    }

    public function confirmGrantAccess()
    {
        $this->grantAccess($this->selectedUserId, $this->selectedUserName, $this->selectedUserEmail);
    }

    public function confirmRevokeAccess()
    {
        $this->revokeAccess($this->selectedUserId, $this->selectedUserName);
    }

    public function confirmMakeAdmin()
    {
        $this->makeAdmin($this->selectedUserId, $this->selectedUserName);
    }

    public function openEditModal($userId)
    {
        if (!$this->dbUsers->has($userId)) {
            $this->noreloadNotif('failed', 'Access Denied', 'User must be granted access first.');
            return;
        }

        $user = $this->dbUsers->get($userId);
        $this->editUserId = $userId;
        $this->editFarm = $user->farm;
        $this->editDepartment = $user->department;

        $this->dispatch('open-edit-modal');
    }

    public function updateUserDetails()
    {
        try {
            $user = User::find($this->editUserId);

            if (!$user) {
                $this->noreloadNotif('failed', 'User Not Found', 'User not found in system.');
                return;
            }

            $user->update([
                'farm' => $this->editFarm,
                'department' => $this->editDepartment,
            ]);

            Cache::forget('user_access_db_users');
            $this->dbUsers->put($this->editUserId, $user);

            $this->noreloadNotif('success', 'Updated', 'User details updated successfully.');
            $this->reset(['editUserId', 'editFarm', 'editDepartment']);

        } catch (\Exception $e) {
            $this->noreloadNotif('failed', 'Update Failed', 'Failed to update user: ' . $e->getMessage());
            Log::error('Update user error: ' . $e->getMessage());
        }
    }

    private function grantAccess($userId, $name, $email)
    {
        try {
            if ($this->dbUsers->has($userId)) {
                $this->noreloadNotif('failed', 'Access Denied', 'User already has access.');
                return;
            }

            $user = User::create([
                'id' => $userId,
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('default_password_' . rand(1000, 9999)),
                'farm' => null,
                'department' => null,
            ]);

            Cache::forget('user_access_db_users');
            $this->dbUsers->put($userId, $user);

            $this->noreloadNotif('success', 'Access Granted', 'Access granted to ' . $user->name);
            Log::info('User granted access: ' . $user->email);

        } catch (\Exception $e) {
            $this->noreloadNotif('failed', 'Grant Access Failed', 'Failed to grant access: ' . $e->getMessage());
            Log::error('Grant access error: ' . $e->getMessage());
        }
    }

    private function revokeAccess($userId, $name)
    {
        try {
            $user = User::find($userId);

            if (!$user) {
                $this->noreloadNotif('failed', 'User Not Found', 'User not found in system.');
                return;
            }

            $user->delete();

            Cache::forget('user_access_db_users');
            $this->dbUsers->forget($userId);

            $this->noreloadNotif('success', 'Access Revoked', 'Access revoked for ' . $name);
            Log::info('User access revoked: ' . $userId);

        } catch (\Exception $e) {
            $this->noreloadNotif('failed', 'Revoke Access Failed', 'Failed to revoke access: ' . $e->getMessage());
            Log::error('Revoke access error: ' . $e->getMessage());
        }
    }

    private function makeAdmin($userId, $name)
    {
        try {
            $user = User::find($userId);

            if (!$user) {
                $this->noreloadNotif('failed', 'User Not Found', 'User not found in system.');
                return;
            }

            if ($user->is_admin) {
                $this->noreloadNotif('failed', 'Already Admin', $name . ' is already an admin.');
                return;
            }

            $user->update(['is_admin' => true]);

            Cache::forget('user_access_db_users');
            $this->dbUsers->put($userId, $user);

            $this->noreloadNotif('success', 'Admin Granted', $name . ' is now an admin.');
            Log::info('User made admin: ' . $user->email);

        } catch (\Exception $e) {
            $this->noreloadNotif('failed', 'Make Admin Failed', 'Failed to make admin: ' . $e->getMessage());
            Log::error('Make admin error: ' . $e->getMessage());
        }
    }

    private function noreloadNotif($type, $header, $message): void
    {
        $this->dispatch('notif', type: $type, header: $header, message: $message);
    }

    public function render()
    {
        return view('livewire.user-access', [
            'users' => $this->paginateUsers(),
        ]);
    }
}
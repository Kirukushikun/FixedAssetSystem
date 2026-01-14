<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Department;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class UserAccess extends Component
{   
    public $users = [];
    public $dbUsers = [];
    public $departments = [];
    
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
        $this->fetchUsers();
        
        // Cache database users for 10 minutes
        $this->dbUsers = Cache::remember('user_access_db_users', 600, function () {
            return User::all()->keyBy('id');
        });

        $this->departments = Department::all();
    }

    public function fetchUsers()
    {
        // Cache API users for 5 minutes since this is external data
        $this->users = Cache::remember('user_access_api_users', 300, function () {
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
                
                // Decrypt the user IDs
                return array_map(function($user) {
                    try {
                        $user['id'] = Crypt::decryptString($user['id']);
                    } catch (\Exception $e) {
                        Log::error('Failed to decrypt user ID for: ' . $user['first_name'] . ' ' . $user['last_name']);
                    }
                    return $user;
                }, $users);
            } else {
                session()->flash('error', 'Failed to fetch users. Status: ' . $response->status());
                Log::info('API Error: ' . $response->status());
                return [];
            }
        });

        if (!empty($this->users)) {
            Log::info('Fetched Users:', $this->users);
        }
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

            // Clear cache after update
            Cache::forget('user_access_db_users');
            
            // Update the dbUsers collection
            $this->dbUsers->put($this->editUserId, $user);

            $this->noreloadNotif('success', 'Updated', 'User details updated successfully.');
            Log::info('User details updated: ' . $user->email);
            
            // Reset edit properties
            $this->reset(['editUserId', 'editFarm', 'editDepartment']);
            
        } catch (\Exception $e) {
            $this->noreloadNotif('failed', 'Update Failed', 'Failed to update user: ' . $e->getMessage());
            Log::error('Update user error: ' . $e->getMessage());
        }
    }

    private function grantAccess($userId, $name, $email)
    {
        try {
            // Check if user already exists
            if ($this->dbUsers->has($userId)) {
                $this->noreloadNotif('failed', 'Access Denied', 'User already has access.');
                return;
            }

            // Create new user in local database using their actual ID
            $user = User::create([
                'id' => $userId, // Use their actual employee ID as the primary key
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('default_password_' . rand(1000, 9999)),
                'farm' => null,
                'department' => null,
            ]);

            // Clear cache after creating user
            Cache::forget('user_access_db_users');
            
            // Update the dbUsers collection with the new user
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
            
            // Clear cache after deletion
            Cache::forget('user_access_db_users');
            
            // Remove from dbUsers collection
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

            // Check if user is already an admin
            if ($user->is_admin) {
                $this->noreloadNotif('failed', 'Already Admin', $name . ' is already an admin.');
                return;
            }

            $user->update([
                'is_admin' => true,
            ]);

            // Clear cache after update
            Cache::forget('user_access_db_users');
            
            // Update the dbUsers collection
            $this->dbUsers->put($userId, $user);

            $this->noreloadNotif('success', 'Admin Granted', $name . ' is now an admin.');
            Log::info('User made admin: ' . $user->email);
            
        } catch (\Exception $e) {
            $this->noreloadNotif('failed', 'Make Admin Failed', 'Failed to make admin: ' . $e->getMessage());
            Log::error('Make admin error: ' . $e->getMessage());
        }
    }

    private function noreloadNotif($type, $header, $message){
        $this->dispatch('notif', type: $type, header: $header, message: $message);
    }

    public function render()
    {
        return view('livewire.user-access');
    }
}
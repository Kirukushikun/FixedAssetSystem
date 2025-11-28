<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class UserAccess extends Component
{   
    public $users = [];
    public $dbUsers = [];

    public function mount()
    {
        $this->fetchUsers();
        
        // Get all registered users keyed by id (not employee_id)
        $this->dbUsers = User::all()->keyBy('id');
    }

    public function fetchUsers()
    {
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
            $this->users = array_map(function($user) {
                try {
                    $user['id'] = Crypt::decryptString($user['id']);
                } catch (\Exception $e) {
                    Log::error('Failed to decrypt user ID for: ' . $user['first_name'] . ' ' . $user['last_name']);
                }
                return $user;
            }, $users);

            Log::info('Fetched Users:', $this->users);
        } else {
            $this->users = [];
            session()->flash('error', 'Failed to fetch users. Status: ' . $response->status());
            Log::info('API Error: ' . $response->status());
        }
    }

    public function grantAccess($userId, $name, $email)
    {
        try {
            // Check if user already exists
            if ($this->dbUsers->has($userId)) {
                session()->flash('error', 'User already has access.');
                return;
            }

            // Create new user in local database using their actual ID
            $user = User::create([
                'id' => $userId, // Use their actual employee ID as the primary key
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('default_password_' . rand(1000, 9999)),
            ]);

            // Update the dbUsers collection with the new user
            $this->dbUsers->put($userId, $user);

            session()->flash('success', 'Access granted to ' . $user->name);
            Log::info('User granted access: ' . $user->email);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to grant access: ' . $e->getMessage());
            Log::error('Grant access error: ' . $e->getMessage());
        }
    }

    public function revokeAccess($userId, $name)
    {
        try {
            $user = User::find($userId);
            
            if (!$user) {
                session()->flash('error', 'User not found in system.');
                return;
            }

            $user->delete();
            
            // Remove from dbUsers collection
            $this->dbUsers->forget($userId);

            session()->flash('success', 'Access revoked for ' . $name);
            Log::info('User access revoked: ' . $userId);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to revoke access: ' . $e->getMessage());
            Log::error('Revoke access error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.user-access');
    }
}
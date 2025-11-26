<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class UserAccess extends Component
{   
    public $users = [];

    public function mount()
    {
        $this->fetchUsers();
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

            // Check if data key exists, otherwise store entire response
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

    public function render()
    {
        return view('livewire.user-access');
    }
}

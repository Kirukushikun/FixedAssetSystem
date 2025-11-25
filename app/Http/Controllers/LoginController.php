<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function postLogin(Request $request)
    {
        try {
            // External API config
            $base_uri = config('services.auth_api.base_uri');
            $api_key = config('services.auth_api.api_key');
            $auth_user_api_key = config('services.auth_api.auth_user_api_key');

            // 1. Authenticate using external API
            $authResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $api_key,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($base_uri . '/api/v1/auth/login', [
                'email' => $request->email,
                'password' => $request->password,
            ]);

            if ($authResponse->successful()) {
                $data = $authResponse->json();

                $token = $data['token'] ?? null;
                $token_expires = $data['expires_at'] ?? null;
                $email = $data['email'] ?? null;

                // Store session data
                session([
                    'auth_token' => $token,
                    'token_expires' => $token_expires,
                    'email' => $email
                ]);

                // 2. Get the user ID from Authenticator system
                $userResponse = Http::withHeaders([
                    'x-api-key' => $auth_user_api_key,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->get($base_uri . "/api/v1/users/get-user-id?email=" . $email);

                if (!$userResponse->successful()) {
                    return back()->withErrors([
                        'login' => 'Failed to retrieve user information.'
                    ])->withInput();
                }

                $userData = $userResponse->json();
                $user = User::find($userData['id'] ?? null);

                if ($user) {
                    $this->logAccess($email, true, $request);
                    Auth::loginUsingId($user->id);
                    return redirect()->route('dashboard');
                }

                // User exists in Authenticator but NOT in this system
                $this->logAccess($email, false, $request);
                return back()->withErrors([
                    'login' => 'You are not authorized to access this system.'
                ])->withInput();
            }

            // External API authentication failed
            $this->logAccess($request->input('email'), false, $request);
            return back()->withErrors([
                'login' => $authResponse->json()['message'] ?? 'Incorrect username or password.'
            ])->withInput();

        } catch (\Exception $e) {
            $this->logAccess($request->input('email'), false, $request);
            return back()->withErrors([
                'login' => 'Authentication failed: ' . $e->getMessage()
            ])->withInput();
        }
    }

    private function logAccess($email, $success, Request $request)
    {        
        \App\Models\AccessLog::create([
            'email' => $email,
            'success' => $success,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}

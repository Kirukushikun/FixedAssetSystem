<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class LoginController extends Controller
{
    const MAX_ATTEMPTS = 3;
    const LOCKOUT_TIME = 900; // 15 minutes in seconds

    public function postLogin(Request $request)
    {
        $email = $request->input('email');
        
        // Check if account is locked
        if ($this->isLocked($email)) {
            $remainingTime = $this->getRemainingLockoutTime($email);
            return back()->withErrors([
                'login' => "Account temporarily locked due to multiple failed attempts. Please try again in {$remainingTime} minutes."
            ])->withInput();
        }

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
                'email' => $email,
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
                    // Clear failed attempts on successful login
                    $this->clearAttempts($email);
                    
                    $this->logAccess($email, true, $request);
                    Auth::loginUsingId($user->id);
                    return redirect()->route('dashboard');
                }

                // User exists in Authenticator but NOT in this system
                $this->incrementAttempts($email);
                $this->logAccess($email, false, $request);
                
                return back()->withErrors([
                    'login' => 'You are not authorized to access this system.'
                ])->withInput();
            }

            // External API authentication failed
            $this->incrementAttempts($email);
            $this->logAccess($email, false, $request);
            
            $attemptsLeft = self::MAX_ATTEMPTS - $this->getAttempts($email);
            $errorMessage = $authResponse->json()['message'] ?? 'Incorrect username or password.';
            
            if ($attemptsLeft > 0) {
                $errorMessage .= " You have {$attemptsLeft} attempt(s) remaining.";
            }
            
            return back()->withErrors([
                'login' => $errorMessage
            ])->withInput();

        } catch (\Exception $e) {
            $this->incrementAttempts($email);
            $this->logAccess($email, false, $request);
            
            return back()->withErrors([
                'login' => 'Authentication failed: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Check if the account is locked
     */
    private function isLocked($email)
    {
        return Cache::has($this->getLockoutKey($email));
    }

    /**
     * Get number of failed attempts
     */
    private function getAttempts($email)
    {
        return Cache::get($this->getAttemptsKey($email), 0);
    }

    /**
     * Increment failed login attempts
     */
    private function incrementAttempts($email)
    {
        $key = $this->getAttemptsKey($email);
        $attempts = $this->getAttempts($email) + 1;
        
        // Store attempts for 15 minutes
        Cache::put($key, $attempts, now()->addMinutes(15));
        
        // Lock account if max attempts reached
        if ($attempts >= self::MAX_ATTEMPTS) {
            Cache::put($this->getLockoutKey($email), true, self::LOCKOUT_TIME);
        }
    }

    /**
     * Clear failed login attempts
     */
    private function clearAttempts($email)
    {
        Cache::forget($this->getAttemptsKey($email));
        Cache::forget($this->getLockoutKey($email));
    }

    /**
     * Get remaining lockout time in minutes
     */
    private function getRemainingLockoutTime($email)
    {
        $key = $this->getLockoutKey($email);
        $expiresAt = Cache::get($key);
        
        if ($expiresAt === true) {
            // If using simple true value, estimate remaining time
            return ceil(self::LOCKOUT_TIME / 60);
        }
        
        return ceil(Cache::get($key, 0) / 60);
    }

    /**
     * Get cache key for attempts counter
     */
    private function getAttemptsKey($email)
    {
        return 'login_attempts_' . sha1($email);
    }

    /**
     * Get cache key for lockout
     */
    private function getLockoutKey($email)
    {
        return 'login_lockout_' . sha1($email);
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

    private function reloadNotif($type, $header, $message)
    {
        session()->flash('notif', [
            'type' => $type,
            'header' => $header,
            'message' => $message
        ]);
    }
}
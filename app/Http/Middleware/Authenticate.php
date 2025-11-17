<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\GeneralController;
use App\Models\User;

class Authenticate
{
    public function handle(Request $request, Closure $next)
    {
        $encrypted = $request->query('uid') ?? Session::get('uid');

        if (!$encrypted) {
            return redirect()->route('login');
        }

        // decrypt ID (returns null if invalid)
        $id = GeneralController::decryptString($encrypted);

        if (!$id) {
            return redirect()->route('login');
        }

        $user = User::find($id);

        if (!$user) {
            return redirect()->route('login');
        }

        // Keep user session
        Session::put('uid', $encrypted);
        Session::put('auth_user', $user);

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserAgreedToDataUsage
{
    public function handle(Request $request, Closure $next)
    {
        // Ensure user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // Check if user has NOT agreed
            if (!$user->agreed_to_data_usage) {
                return redirect()->route('dashboard') // Or your named route
                                 ->with('error', 'You must agree to the data usage policy.');
            }
        }

        return $next($request);
    }
}


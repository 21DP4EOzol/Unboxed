<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Check if the user has two-factor authentication enabled
        if ($user && $user->two_factor_confirmed_at) {
            // Check if the user has a valid session flag indicating 2FA completion
            if (!session('two_factor_authenticated')) {
                return redirect()->route('two-factor.challenge');
            }
        }
        
        return $next($request);
    }
}
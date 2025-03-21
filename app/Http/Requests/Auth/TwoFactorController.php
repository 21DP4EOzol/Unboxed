<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\TwoFactorCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    /**
     * Display the two-factor challenge view.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->two_factor_code && $user->two_factor_expires_at && $user->two_factor_expires_at->gt(now())) {
            return inertia('Auth/TwoFactorChallenge', [
                'remainingTime' => $user->two_factor_expires_at->diffInSeconds(now()),
            ]);
        }
        
        // Generate a new code if no valid code exists
        $user->generateTwoFactorCode();
        $user->notify(new TwoFactorCode);
        
        return inertia('Auth/TwoFactorChallenge', [
            'remainingTime' => 600, // 10 minutes in seconds
        ]);
    }
    
    /**
     * Verify the two-factor authentication code.
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:6',
        ]);
        
        $user = Auth::user();
        
        // Check if the code is correct and not expired
        if ($validated['code'] == $user->two_factor_code && $user->two_factor_expires_at->gt(now())) {
            // Reset the code after successful verification
            $user->resetTwoFactorCode();

            // Set a session flag to indicate that 2FA has been completed for this session
            session(['two_factor_authenticated' => true]);
            
            // Continue to the intended page
            return redirect()->intended(route('dashboard', absolute: false));
        }
        
        return back()->withErrors(['code' => 'The code is invalid or has expired.']);
    }
    
    /**
     * Resend the two-factor authentication code.
     */
    public function resend()
    {
        $user = Auth::user();
        
        // Reset any existing code
        $user->resetTwoFactorCode();
        
        // Generate a new code
        $user->generateTwoFactorCode();
        $user->notify(new TwoFactorCode);
        
        return back()->with('status', 'A new verification code has been sent to your email.');
    }
}
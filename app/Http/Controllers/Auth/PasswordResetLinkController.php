<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $throttleKey = 'forgot-password|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);
            throw ValidationException::withMessages([
                'email' => "Terlalu banyak permintaan reset password. Silakan coba lagi dalam {$minutes} menit.",
            ]);
        }

        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'Alamat email tidak terdaftar dalam sistem kami.',
        ]);

        RateLimiter::hit($throttleKey, 600); // Hit the rate limiter to prevent flooding

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __($status),
                ]);
            }
            return back()->with('status', __($status));
        }

        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}

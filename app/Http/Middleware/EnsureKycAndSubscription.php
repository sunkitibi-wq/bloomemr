<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureKycAndSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If not logged in, let standard auth middleware handle it
        if (! $user) {
            return $next($request);
        }

        // Exempt system administrators, super admins, and guardians/patients
        if ($user->is_system_admin || $user->role === 'super_admin' || $user->role === 'guardian') {
            return $next($request);
        }

        // Exempt KYC, subscription, and auth routes to avoid redirect loops
        $exemptPaths = [
            'kyc',
            'subscribe',
            'logout',
            'login',
            'register',
        ];

        foreach ($exemptPaths as $path) {
            if ($request->is($path) || $request->is($path.'/*')) {
                return $next($request);
            }
        }

        // 1. Enforce KYC Verification
        if ($user->kyc_status !== 'approved') {
            return redirect()->route('kyc');
        }

        // 2. Enforce Subscription
        if (! $user->isSubscribed()) {
            return redirect()->route('subscribe');
        }

        return $next($request);
    }
}

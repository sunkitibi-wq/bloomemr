<?php

namespace App\Http\Middleware;

use App\Models\Practice;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyPractice
{
    /**
     * Resolve the active practice from the request host subdomain,
     * bind it to the app container, and enforce active-status checks.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $practice = null;

        // Attempt to resolve via subdomain (e.g. bloomclinic.bloom.test → slug "bloomclinic")
        $parts = explode('.', $host);
        if (count($parts) >= 3) {
            $slug = $parts[0];
            $practice = Practice::where('slug', $slug)->first();
        }

        // Fallback: resolve from authenticated user's practice_id (covers single-domain dev installs)
        if (! $practice && auth()->hasUser()) {
            $practice = auth()->user()->practice_id
                ? Practice::find(auth()->user()->practice_id)
                : null;
        }

        // Fallback for system admins switching tenants via session
        if (! $practice && session()->has('current_practice_id')) {
            $practice = Practice::find(session('current_practice_id'));
        }

        // Reject inactive practices (still allow unauthenticated landing / register pages)
        if ($practice && ! $practice->is_active) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'This practice account is suspended.'], 403);
            }

            abort(403, 'This practice account has been suspended. Please contact support.');
        }

        // Bind into the app container for the duration of this request
        app()->instance('current_practice', $practice);

        return $next($request);
    }
}

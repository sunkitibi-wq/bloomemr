<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePracticeType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $type): Response
    {
        $practice = app()->bound('current_practice') 
            ? app('current_practice') 
            : (auth()->hasUser() ? auth()->user()->practice : null);

        if (! $practice) {
            abort(403, 'No practice context found.');
        }

        $isAuthorized = false;

        if ($type === 'hospital' && $practice->isHospital()) {
            $isAuthorized = true;
        } elseif ($type === 'pharmacy' && $practice->isPharmacy()) {
            $isAuthorized = true;
        }

        if (! $isAuthorized) {
            if ($practice->isPharmacy() && ! $request->routeIs('pharmacy.*')) {
                return redirect()->route('pharmacy.portal');
            }
            
            if ($practice->isHospital() && ! $request->routeIs('hospital.*')) {
                return redirect()->route('hospital.dashboard');
            }

            abort(403, 'Unauthorized access for this practice type.');
        }

        return $next($request);
    }
}

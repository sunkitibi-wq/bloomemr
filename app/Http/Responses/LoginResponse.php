<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract, TwoFactorLoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  Request  $request
     * @return Response
     */
    public function toResponse($request)
    {
        $user = auth()->user() ?? auth('portal')->user();
        
        if ($user?->isSystemAdmin()) {
            return redirect()->route('system.dashboard');
        }

        $role = $user?->role;

        return match ($role) {
            'guardian' => redirect()->route('portal.dashboard'),
            'pharmacist' => redirect()->route('pharmacy.portal'),
            'hospital_administrator', 'super_admin' => redirect()->route('analytics'),
            'attending', 'resident' => redirect()->route('dashboard'),
            default => redirect()->route('dashboard'),
        };
    }
}

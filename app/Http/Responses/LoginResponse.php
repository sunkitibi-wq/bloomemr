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
        $role = auth()->user()?->role ?? auth('portal')->user()?->role;

        return match ($role) {
            'guardian' => redirect()->intended(route('portal.dashboard')),
            'pharmacist' => redirect()->intended(route('pharmacy.portal')),
            'hospital_administrator', 'super_admin' => redirect()->intended(route('analytics')),
            'attending', 'resident' => redirect()->intended(route('dashboard')),
            default => redirect()->intended(route('dashboard')),
        };
    }
}

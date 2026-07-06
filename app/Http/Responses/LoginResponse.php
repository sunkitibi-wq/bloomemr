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

        if ($role === 'guardian') {
            return redirect()->intended(route('portal.dashboard'));
        }

        if ($role === 'pharmacist') {
            return redirect()->intended(route('pharmacy.portal'));
        }

        return redirect()->intended(route('dashboard'));
    }
}

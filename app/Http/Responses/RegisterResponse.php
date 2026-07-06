<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Symfony\Component\HttpFoundation\Response;

class RegisterResponse implements RegisterResponseContract
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

        return $role === 'guardian'
            ? redirect()->intended(route('portal.dashboard'))
            : redirect()->intended(route('dashboard'));
    }
}

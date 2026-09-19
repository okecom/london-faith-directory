<?php

namespace App\Http\Responses;

use App\Models\User;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        $route = match ($user->role) {
            User::ROLE_REGISTERED_USER => 'user.dashboard',
            User::ROLE_ORGANISATION_ADMIN => 'organisation.dashboard',
            User::ROLE_SITE_ADMIN => 'site.dashboard',
            default => null,
        };

        abort_if(
            $route === null,
            403,
            'Your account does not have a valid role.'
        );

        return redirect()->route($route);
    }
}
<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserCanManage
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        abort_unless(
            $user &&
            in_array(
                $user->role,
                [
                    User::ROLE_SITE_ADMIN,
                    User::ROLE_ORGANISATION_ADMIN,
                ],
                true
            ),
            403,
            'You are not authorised to access management pages.'
        );

        return $next($request);
    }
}
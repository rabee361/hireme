<?php

namespace App\Http\Middleware;

use App\Enums\UserType;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_if(
            ! $user || $user->type !== UserType::Admin || ! $user->is_verified,
            Response::HTTP_FORBIDDEN,
            'This action is unauthorized.'
        );

        return $next($request);
    }
}
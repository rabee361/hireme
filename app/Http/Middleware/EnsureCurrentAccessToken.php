<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCurrentAccessToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('api');

        if (! $user) {
            return new JsonResponse([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $payload = auth('api')->payload();

        if ($payload->get('token_type') !== 'access') {
            return new JsonResponse([
                'message' => 'The provided token is not an access token.',
            ], 401);
        }

        if ((int) $payload->get('token_version', -1) !== (int) $user->token_version) {
            return new JsonResponse([
                'message' => 'The token is no longer valid.',
            ], 401);
        }

        return $next($request);
    }
}
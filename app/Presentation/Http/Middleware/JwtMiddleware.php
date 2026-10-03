<?php

declare(strict_types=1);

namespace App\Presentation\Http\Middleware;

use App\Application\Ports\Outbound\ITokenGeneratorPort;
use App\Presentation\Http\ProblemDetails\ProblemDetailsResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
    public function __construct(
        private readonly ITokenGeneratorPort $tokenGenerator
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return ProblemDetailsResponse::create(
                title: 'No autorizado',
                detail: 'Token de autorización ausente o con formato inválido',
                status: 401
            );
        }

        $token = substr($authHeader, 7);
        $payload = $this->tokenGenerator->validateToken($token);

        if (!$payload) {
            return ProblemDetailsResponse::create(
                title: 'No autorizado',
                detail: 'El token proporcionado ha expirado o es inválido',
                status: 401
            );
        }

        $request->attributes->set('auth_user', $payload);

        return $next($request);
    }
}

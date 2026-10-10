<?php

declare(strict_types=1);

use App\Domain\Exceptions\ConcurrencyConflictException;
use App\Domain\Exceptions\DomainException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        apiPrefix: '',
    )
    ->withMiddleware(function (Middleware $middleware) {
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ConcurrencyConflictException $e, Request $request): Response {
            return new JsonResponse([
                'title' => 'Conflicto con otra operación simultánea',
                'status' => 409,
                'detail' => $e->getMessage(),
            ], 409, [
                'Content-Type' => 'application/problem+json; charset=utf-8',
            ]);
        });

        $exceptions->render(function (DomainException $e, Request $request): Response {
            $status = $e->getCode() ?: 422;
            if ($status === 404) {
                return response('', 404, ['Content-Length' => '0']);
            }
            return new JsonResponse([
                'title' => 'Regla de negocio violada',
                'status' => $status,
                'detail' => $e->getMessage(),
            ], $status, [
                'Content-Type' => 'application/problem+json; charset=utf-8',
            ]);
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request): Response {
            return response('', 404, ['Content-Length' => '0']);
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request): Response {
            $headers = $e->getHeaders();
            $headers['Content-Length'] = '0';
            return response('', 405, $headers);
        });

        $exceptions->render(function (\Throwable $e, Request $request): ?Response {
            if ($request->is('api/*') || $request->is('media/*') || $request->is('health')) {
                return new JsonResponse([
                    'title' => 'Error interno',
                    'status' => 500,
                    'detail' => 'Ocurrió un error inesperado.',
                ], 500, [
                    'Content-Type' => 'application/problem+json; charset=utf-8',
                ]);
            }
            return null;
        });
    })->create();

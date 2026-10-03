<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Application\DTO\LoginDTO;
use App\Application\Ports\Inbound\IAuthenticateUserUseCasePort;
use App\Domain\Exception\DomainValidationException;
use App\Presentation\Http\ProblemDetails\ProblemDetailsResponse;
use App\Presentation\Http\Request\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    public function __construct(
        private readonly IAuthenticateUserUseCasePort $authenticateUserUseCase
    ) {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $dto = new LoginDTO(
                email: (string) $request->input('email'),
                password: (string) $request->input('password')
            );

            $authResponse = $this->authenticateUserUseCase->execute($dto);

            return response()->json([
                'token' => $authResponse->token,
                'user' => [
                    'id' => $authResponse->userId,
                    'name' => $authResponse->userName,
                    'email' => $authResponse->email,
                    'role' => $authResponse->role,
                ],
            ]);
        } catch (DomainValidationException $e) {
            return ProblemDetailsResponse::create(
                title: 'Error de Autenticación',
                detail: $e->getMessage(),
                status: 401
            );
        }
    }
}

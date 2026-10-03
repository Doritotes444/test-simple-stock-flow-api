<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\AuthResponseDTO;
use App\Application\DTO\LoginDTO;
use App\Application\Ports\Inbound\IAuthenticateUserUseCasePort;
use App\Application\Ports\Outbound\IPasswordHasherPort;
use App\Application\Ports\Outbound\ITokenGeneratorPort;
use App\Application\Ports\Outbound\IUserRepositoryPort;
use App\Domain\Exception\DomainValidationException;
use App\Domain\ValueObject\Email;

class AuthenticateUserUseCase implements IAuthenticateUserUseCasePort
{
    public function __construct(
        private readonly IUserRepositoryPort $userRepository,
        private readonly IPasswordHasherPort $passwordHasher,
        private readonly ITokenGeneratorPort $tokenGenerator
    ) {
    }

    public function execute(LoginDTO $dto): AuthResponseDTO
    {
        $email = new Email($dto->email);
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !$this->passwordHasher->verify($dto->password, $user->getPasswordHash())) {
            throw new DomainValidationException("Credenciales de acceso inválidas");
        }

        $token = $this->tokenGenerator->generateToken($user);

        return new AuthResponseDTO(
            token: $token,
            userId: (int) $user->getId(),
            userName: $user->getName(),
            email: $user->getEmail()->getValue(),
            role: $user->getRole()
        );
    }
}

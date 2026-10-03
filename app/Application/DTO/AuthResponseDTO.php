<?php

declare(strict_types=1);

namespace App\Application\DTO;

final readonly class AuthResponseDTO
{
    public function __construct(
        public string $token,
        public int $userId,
        public string $userName,
        public string $email,
        public string $role
    ) {
    }
}

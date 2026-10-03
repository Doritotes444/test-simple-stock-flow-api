<?php

declare(strict_types=1);

namespace App\Application\DTO;

final readonly class LoginDTO
{
    public function __construct(
        public string $email,
        public string $password
    ) {
    }
}

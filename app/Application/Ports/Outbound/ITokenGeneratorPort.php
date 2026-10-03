<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

use App\Domain\Model\User;

interface ITokenGeneratorPort
{
    public function generateToken(User $user): string;
    public function validateToken(string $token): ?array;
}

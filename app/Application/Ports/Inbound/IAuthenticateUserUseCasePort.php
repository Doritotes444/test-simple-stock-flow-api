<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

use App\Application\DTO\LoginDTO;
use App\Application\DTO\AuthResponseDTO;

interface IAuthenticateUserUseCasePort
{
    public function execute(LoginDTO $dto): AuthResponseDTO;
}

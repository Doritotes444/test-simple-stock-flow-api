<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class InvalidRoleException extends DomainException
{
    public function __construct(string $role)
    {
        parent::__construct(sprintf("Rol no válido: '%s'.", $role));
    }
}

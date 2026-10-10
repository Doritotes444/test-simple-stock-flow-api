<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class InvalidPriceException extends DomainException
{
    public function __construct(string $message = 'El precio debe ser mayor a cero.')
    {
        parent::__construct($message);
    }
}

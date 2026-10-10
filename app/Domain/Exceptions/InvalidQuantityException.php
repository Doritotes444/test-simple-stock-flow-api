<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class InvalidQuantityException extends DomainException
{
    public function __construct(string $message = 'La cantidad debe ser mayor a cero.')
    {
        parent::__construct($message);
    }
}

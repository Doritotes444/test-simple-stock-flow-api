<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class EmptySaleException extends DomainException
{
    public function __construct(string $message = 'La venta debe tener al menos un ítem.')
    {
        parent::__construct($message);
    }
}

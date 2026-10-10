<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class ConcurrencyConflictException extends DomainException
{
    public function __construct(
        string $message = 'Otra operación modificó los datos al mismo tiempo. Inténtalo de nuevo.'
    ) {
        parent::__construct($message, 409);
    }
}

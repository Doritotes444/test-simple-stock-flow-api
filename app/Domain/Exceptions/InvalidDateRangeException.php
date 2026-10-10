<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class InvalidDateRangeException extends DomainException
{
    public function __construct(string $message = 'La fecha final no puede ser anterior a la inicial.')
    {
        parent::__construct($message);
    }
}

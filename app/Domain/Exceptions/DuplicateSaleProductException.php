<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class DuplicateSaleProductException extends DomainException
{
    public function __construct(string $message = 'La venta tiene productos repetidos.')
    {
        parent::__construct($message);
    }
}

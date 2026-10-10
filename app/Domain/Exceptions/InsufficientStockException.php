<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class InsufficientStockException extends DomainException
{
    public function __construct(string $productName, int $available, int $requested)
    {
        parent::__construct(
            sprintf("Stock insuficiente para '%s': disponible %d, solicitado %d.", $productName, $available, $requested)
        );
    }
}

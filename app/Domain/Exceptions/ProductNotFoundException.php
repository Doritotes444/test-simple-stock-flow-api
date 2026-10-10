<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class ProductNotFoundException extends DomainException
{
    public function __construct(string $productId)
    {
        parent::__construct(sprintf("El producto %s no existe.", $productId));
    }
}

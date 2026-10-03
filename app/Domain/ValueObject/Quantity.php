<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\DomainValidationException;

final readonly class Quantity
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new DomainValidationException("La cantidad solicitada debe ser estrictamente mayor a cero: {$value}");
        }

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\InsufficientStockException;
use App\Domain\Exception\DomainValidationException;

final readonly class Stock
{
    private int $units;

    public function __construct(int $units)
    {
        if ($units < 0) {
            throw new DomainValidationException("El stock de un producto no puede ser negativo: {$units}");
        }

        $this->units = $units;
    }

    public function getUnits(): int
    {
        return $this->units;
    }

    public function canFulfill(Quantity $requested): bool
    {
        return $this->units >= $requested->getValue();
    }

    public function decrease(Quantity $quantity): Stock
    {
        if (!$this->canFulfill($quantity)) {
            throw new InsufficientStockException("Stock insuficiente: disponible {$this->units}, solicitado {$quantity->getValue()}");
        }

        return new Stock($this->units - $quantity->getValue());
    }

    public function increase(int $units): Stock
    {
        if ($units < 0) {
            throw new DomainValidationException("No se puede incrementar stock con valor negativo: {$units}");
        }

        return new Stock($this->units + $units);
    }
}

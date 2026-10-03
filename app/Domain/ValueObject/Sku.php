<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\DomainValidationException;

final readonly class Sku
{
    private string $code;

    public function __construct(string $code)
    {
        $trimmed = strtoupper(trim($code));
        if (strlen($trimmed) < 3 || strlen($trimmed) > 30) {
            throw new DomainValidationException("El código SKU debe tener entre 3 y 30 caracteres: {$code}");
        }

        $this->code = $trimmed;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}

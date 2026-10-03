<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\DomainValidationException;

final readonly class Email
{
    private string $value;

    public function __construct(string $value)
    {
        $sanitized = trim(strtolower($value));
        if (!filter_var($sanitized, FILTER_VALIDATE_EMAIL)) {
            throw new DomainValidationException("El formato del correo electrónico es inválido: {$value}");
        }

        $this->value = $sanitized;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}

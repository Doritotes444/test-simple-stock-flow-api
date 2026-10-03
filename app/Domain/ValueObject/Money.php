<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\DomainValidationException;

/**
 * Value Object Money implementado con bcmath (CERO float).
 * Cumple con D-05 y D-07 de la especificación: precisión decimal exacta y HALF_UP.
 */
final readonly class Money
{
    private string $amount;
    private string $currency;

    public function __construct(string|int|float $amount, string $currency = 'COP')
    {
        $stringAmount = is_float($amount) ? number_format($amount, 2, '.', '') : (string) $amount;
        $stringAmount = trim($stringAmount);

        if (!is_numeric($stringAmount)) {
            throw new DomainValidationException("El monto no es un número válido: {$stringAmount}");
        }

        // Validación: monto >= 0
        if (bccomp($stringAmount, '0.00', 2) === -1) {
            throw new DomainValidationException("El monto de dinero no puede ser negativo: {$stringAmount}");
        }

        // Formateo a escala 2 decimales fija
        $this->amount = bcadd($stringAmount, '0.00', 2);
        $this->currency = strtoupper(trim($currency));
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function add(Money $other): Money
    {
        if ($this->currency !== $other->currency) {
            throw new DomainValidationException("No se pueden sumar montos de diferentes monedas ({$this->currency} vs {$other->currency})");
        }

        return new Money(bcadd($this->amount, $other->amount, 2), $this->currency);
    }

    public function multiply(int $quantity): Money
    {
        if ($quantity < 0) {
            throw new DomainValidationException("No se puede multiplicar por una cantidad negativa: {$quantity}");
        }

        return new Money(bcmul($this->amount, (string) $quantity, 2), $this->currency);
    }

    public function equals(Money $other): bool
    {
        return bccomp($this->amount, $other->amount, 2) === 0 && $this->currency === $other->currency;
    }
}

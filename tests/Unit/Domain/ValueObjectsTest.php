<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Domain\Exceptions\InvalidDateRangeException;
use App\Domain\Exceptions\InvalidQuantityException;
use App\Domain\ValueObjects\DateRange;
use App\Domain\ValueObjects\Money;
use App\Domain\ValueObjects\Quantity;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ValueObjectsTest extends TestCase
{
    /**
     * RN-03: Quantity mayor que cero
     */
    public function test_rn_03_quantity_must_be_strictly_positive(): void
    {
        $q = Quantity::of(5);
        $this->assertSame(5, $q->value());

        $this->expectException(InvalidQuantityException::class);
        Quantity::of(0);
    }

    public function test_money_exact_addition_and_multiplication(): void
    {
        $m1 = Money::of(100.25);
        $m2 = Money::of(50.50);
        $sum = $m1->plus($m2);

        $this->assertSame(150.75, $sum->amount());
        $this->assertSame('COP', $sum->currency());

        $prod = $m1->multiply(2);
        $this->assertSame(200.50, $prod->amount());
    }

    public function test_date_range_validation(): void
    {
        $from = new DateTimeImmutable('2026-01-01T00:00:00Z');
        $to = new DateTimeImmutable('2026-01-31T23:59:59Z');

        $range = DateRange::of($from, $to);
        $this->assertSame($from, $range->from());
        $this->assertSame($to, $range->to());

        $this->expectException(InvalidDateRangeException::class);
        DateRange::of($to, $from);
    }
}
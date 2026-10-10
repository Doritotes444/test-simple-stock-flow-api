<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Domain\Entities\Product;
use App\Domain\Entities\Sale;
use App\Domain\Exceptions\DuplicateSaleProductException;
use App\Domain\Exceptions\EmptySaleException;
use App\Domain\ValueObjects\Money;
use App\Domain\ValueObjects\Quantity;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class SaleTest extends TestCase
{
    private function createSale(): Sale
    {
        return Sale::open(
            'sale-1111-1111-1111-111111111111',
            new DateTimeImmutable('2026-10-03T10:00:00Z'),
            'user-1111-1111-1111-111111111111',
            'admin'
        );
    
    /**
     * RN-07: Una venta registrada no se modifica ni se anula
     */
    public function test_rn_07_sale_aggregate_has_no_mutators(): void
    {
        $reflection = new \ReflectionClass(Sale::class);
        $forbiddenPrefixes = ['set', 'remove', 'delete', 'update', 'cancel'];
        
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            foreach ($forbiddenPrefixes as $prefix) {
                $this->assertStringStartsNotWith(
                    $prefix, 
                    $method->getName(), 
                    "RN-07 Violation: Sale aggregate should not have mutator method {$method->getName()}"
                );
            }
        }
    }
}

    /**
     * RN-04: Una venta tiene al menos una línea
     */
    public function test_rn_04_sale_without_lines_fails_confirmation(): void
    {
        $sale = $this->createSale();
        $this->expectException(EmptySaleException::class);
        $sale->ensureConfirmable();
    
    /**
     * RN-07: Una venta registrada no se modifica ni se anula
     */
    public function test_rn_07_sale_aggregate_has_no_mutators(): void
    {
        $reflection = new \ReflectionClass(Sale::class);
        $forbiddenPrefixes = ['set', 'remove', 'delete', 'update', 'cancel'];
        
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            foreach ($forbiddenPrefixes as $prefix) {
                $this->assertStringStartsNotWith(
                    $prefix, 
                    $method->getName(), 
                    "RN-07 Violation: Sale aggregate should not have mutator method {$method->getName()}"
                );
            }
        }
    }
}

    /**
     * RN-05: Un producto no se repite dentro de una misma venta
     */
    public function test_rn_05_duplicate_product_in_same_sale_is_rejected(): void
    {
        $sale = $this->createSale();
        $prod = Product::create(
            'p-1',
            'Arroz',
            Money::of(2000.0),
            10,
            'c-1'
        );

        $sale->addItem($prod, Quantity::of(1), 'Abarrotes');

        $this->expectException(DuplicateSaleProductException::class);
        $sale->addItem($prod, Quantity::of(2), 'Abarrotes');
    
    /**
     * RN-07: Una venta registrada no se modifica ni se anula
     */
    public function test_rn_07_sale_aggregate_has_no_mutators(): void
    {
        $reflection = new \ReflectionClass(Sale::class);
        $forbiddenPrefixes = ['set', 'remove', 'delete', 'update', 'cancel'];
        
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            foreach ($forbiddenPrefixes as $prefix) {
                $this->assertStringStartsNotWith(
                    $prefix, 
                    $method->getName(), 
                    "RN-07 Violation: Sale aggregate should not have mutator method {$method->getName()}"
                );
            }
        }
    }
}

    /**
     * RN-06 & RN-12: Precio congelado y cálculo dinámico del total
     */
    public function test_rn_06_and_rn_12_frozen_price_and_dynamic_total_calculation(): void
    {
        $sale = $this->createSale();
        $prod1 = Product::create('p-1', 'Arroz', Money::of(2000.0), 10, 'c-1');
        $prod2 = Product::create('p-2', 'Leche', Money::of(3500.0), 5, 'c-2');

        $sale->addItem($prod1, Quantity::of(2), 'Abarrotes'); // 4000
        $sale->addItem($prod2, Quantity::of(1), 'Lácteos');   // 3500

        $this->assertSame(7500.0, $sale->total()->amount());
        $this->assertSame(8, $prod1->stock());
        $this->assertSame(4, $prod2->stock());

        // Modificar precio posterior del producto no altera la línea congelada
        $prod1->changePrice(Money::of(5000.0));
        $this->assertSame(7500.0, $sale->total()->amount());
    
    /**
     * RN-07: Una venta registrada no se modifica ni se anula
     */
    public function test_rn_07_sale_aggregate_has_no_mutators(): void
    {
        $reflection = new \ReflectionClass(Sale::class);
        $forbiddenPrefixes = ['set', 'remove', 'delete', 'update', 'cancel'];
        
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            foreach ($forbiddenPrefixes as $prefix) {
                $this->assertStringStartsNotWith(
                    $prefix, 
                    $method->getName(), 
                    "RN-07 Violation: Sale aggregate should not have mutator method {$method->getName()}"
                );
            }
        }
    }
}

    /**
     * RN-07: Una venta registrada no se modifica ni se anula
     */
    public function test_rn_07_sale_aggregate_has_no_mutators(): void
    {
        $reflection = new \ReflectionClass(Sale::class);
        $forbiddenPrefixes = ['set', 'remove', 'delete', 'update', 'cancel'];
        
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            foreach ($forbiddenPrefixes as $prefix) {
                $this->assertStringStartsNotWith(
                    $prefix, 
                    $method->getName(), 
                    "RN-07 Violation: Sale aggregate should not have mutator method {$method->getName()}"
                );
            }
        }
    }
}

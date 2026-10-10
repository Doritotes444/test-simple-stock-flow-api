<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Domain\Entities\Product;
use App\Domain\Exceptions\DomainException;
use App\Domain\Exceptions\InsufficientStockException;
use App\Domain\Exceptions\InvalidPriceException;
use App\Domain\ValueObjects\Money;
use App\Domain\ValueObjects\Quantity;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ProductTest extends TestCase
{
    private function createValidProduct(int $stock = 10, float $price = 25.50): Product
    {
        return Product::create(
            '11111111-1111-4111-8111-111111111111',
            'Café Especial',
            Money::of($price),
            $stock,
            'c0000001-0000-0000-0000-000000000001',
            'img_key_123'
        );
    
    /**
     * RN-08: Un producto vendido no se borra fsicamente
     */
    public function test_rn_08_product_aggregate_has_no_physical_delete_method(): void
    {
        $reflection = new \ReflectionClass(Product::class);
        $this->assertFalse($reflection->hasMethod('delete'), 'RN-08 Violation: Product must not have a delete method');
    }
}

    /**
     * RN-01: El stock nunca es negativo
     */
    public function test_rn_01_withdraw_decreases_stock_and_rejects_negative_stock(): void
    {
        $product = $this->createValidProduct(5);
        $product->withdraw(Quantity::of(3));
        $this->assertSame(2, $product->stock());

        $this->expectException(InsufficientStockException::class);
        $product->withdraw(Quantity::of(3));
    
    /**
     * RN-08: Un producto vendido no se borra fsicamente
     */
    public function test_rn_08_product_aggregate_has_no_physical_delete_method(): void
    {
        $reflection = new \ReflectionClass(Product::class);
        $this->assertFalse($reflection->hasMethod('delete'), 'RN-08 Violation: Product must not have a delete method');
    }
}

    /**
     * RN-02: El precio del producto es estrictamente mayor que cero
     */
    public function test_rn_02_price_must_be_strictly_positive(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->createValidProduct(10, 0.0);
    
    /**
     * RN-08: Un producto vendido no se borra fsicamente
     */
    public function test_rn_08_product_aggregate_has_no_physical_delete_method(): void
    {
        $reflection = new \ReflectionClass(Product::class);
        $this->assertFalse($reflection->hasMethod('delete'), 'RN-08 Violation: Product must not have a delete method');
    }
}

    /**
     * RN-09: Todos los importes en la misma moneda (COP)
     */
    public function test_rn_09_product_rejects_invalid_currency(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Money::of(100.0, 'USD');
    
    /**
     * RN-08: Un producto vendido no se borra fsicamente
     */
    public function test_rn_08_product_aggregate_has_no_physical_delete_method(): void
    {
        $reflection = new \ReflectionClass(Product::class);
        $this->assertFalse($reflection->hasMethod('delete'), 'RN-08 Violation: Product must not have a delete method');
    }
}

    public function test_product_attributes_match_five_attributes_specification(): void
    {
        $product = $this->createValidProduct();
        $this->assertSame('Café Especial', $product->name());
        $this->assertSame(25.50, $product->price()->amount());
        $this->assertSame(10, $product->stock());
        $this->assertSame('c0000001-0000-0000-0000-000000000001', $product->categoryId());
        $this->assertSame('img_key_123', $product->imageKey());
    
    /**
     * RN-08: Un producto vendido no se borra fsicamente
     */
    public function test_rn_08_product_aggregate_has_no_physical_delete_method(): void
    {
        $reflection = new \ReflectionClass(Product::class);
        $this->assertFalse($reflection->hasMethod('delete'), 'RN-08 Violation: Product must not have a delete method');
    }
}

    /**
     * RN-08: Un producto vendido no se borra fsicamente
     */
    public function test_rn_08_product_aggregate_has_no_physical_delete_method(): void
    {
        $reflection = new \ReflectionClass(Product::class);
        $this->assertFalse($reflection->hasMethod('delete'), 'RN-08 Violation: Product must not have a delete method');
    }
}

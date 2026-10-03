<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Model\Product;
use App\Domain\ValueObject\Quantity;
use App\Domain\Exception\InsufficientStockException;

class StockDomainService
{
    /**
     * @param array<array{product: Product, quantity: Quantity}> $items
     */
    public function validateAndReduceStock(array $items): void
    {
        foreach ($items as $item) {
            /** @var Product $product */
            $product = $item['product'];
            /** @var Quantity $quantity */
            $quantity = $item['quantity'];

            if (!$product->getStock()->canFulfill($quantity)) {
                throw new InsufficientStockException(
                    "No hay suficiente stock para el producto '{$product->getName()}'. Disponible: {$product->getStock()->getUnits()}, Solicitado: {$quantity->getValue()}"
                );
            }
        }

        // Si todos los items tienen suficiente stock, proceder a la deducción
        foreach ($items as $item) {
            $item['product']->reduceStock($item['quantity']);
        }
    }
}

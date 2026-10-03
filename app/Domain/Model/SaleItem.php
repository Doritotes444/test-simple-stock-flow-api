<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Quantity;

class SaleItem
{
    private ?int $id;
    private int $productId;
    private string $productName;
    private Quantity $quantity;
    private Money $unitPrice;
    private Money $subtotal;

    public function __construct(
        ?int $id,
        int $productId,
        string $productName,
        Quantity $quantity,
        Money $unitPrice
    ) {
        $this->id = $id;
        $this->productId = $productId;
        $this->productName = $productName;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
        $this->subtotal = $unitPrice->multiply($quantity->getValue());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getQuantity(): Quantity
    {
        return $this->quantity;
    }

    public function getUnitPrice(): Money
    {
        return $this->unitPrice;
    }

    public function getSubtotal(): Money
    {
        return $this->subtotal;
    }
}

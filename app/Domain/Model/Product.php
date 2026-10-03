<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Exception\DomainValidationException;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Quantity;
use App\Domain\ValueObject\Sku;
use App\Domain\ValueObject\Stock;

class Product
{
    private ?int $id;
    private Sku $sku;
    private string $name;
    private ?string $description;
    private Money $price;
    private Stock $stock;
    private int $categoryId;
    private bool $isActive;

    public function __construct(
        ?int $id,
        Sku $sku,
        string $name,
        Money $price,
        Stock $stock,
        int $categoryId,
        ?string $description = null,
        bool $isActive = true
    ) {
        $trimmedName = trim($name);
        if (empty($trimmedName)) {
            throw new DomainValidationException("El nombre del producto no puede estar vacío");
        }

        if ($categoryId <= 0) {
            throw new DomainValidationException("El ID de categoría es inválido: {$categoryId}");
        }

        $this->id = $id;
        $this->sku = $sku;
        $this->name = $trimmedName;
        $this->description = $description ? trim($description) : null;
        $this->price = $price;
        $this->stock = $stock;
        $this->categoryId = $categoryId;
        $this->isActive = $isActive;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSku(): Sku
    {
        return $this->sku;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function getStock(): Stock
    {
        return $this->stock;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function reduceStock(Quantity $quantity): void
    {
        if (!$this->isActive) {
            throw new DomainValidationException("No se puede vender un producto inactivo: {$this->name}");
        }

        $this->stock = $this->stock->decrease($quantity);
    }

    public function replenishStock(int $units): void
    {
        $this->stock = $this->stock->increase($units);
    }
}

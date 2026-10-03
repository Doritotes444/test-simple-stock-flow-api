<?php

declare(strict_types=1);

namespace App\Application\DTO;

final readonly class ProductResponseDTO
{
    public function __construct(
        public int $id,
        public string $sku,
        public string $name,
        public ?string $description,
        public float $price,
        public string $currency,
        public int $stock,
        public int $categoryId,
        public bool $isActive
    ) {
    }
}

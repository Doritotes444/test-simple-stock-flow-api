<?php

declare(strict_types=1);

namespace App\Application\DTO;

final readonly class CreateProductDTO
{
    public function __construct(
        public string $sku,
        public string $name,
        public float $price,
        public int $initialStock,
        public int $categoryId,
        public ?string $description = null
    ) {
    }
}

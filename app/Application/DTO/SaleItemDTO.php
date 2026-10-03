<?php

declare(strict_types=1);

namespace App\Application\DTO;

final readonly class SaleItemDTO
{
    public function __construct(
        public int $productId,
        public int $quantity
    ) {
    }
}

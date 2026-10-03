<?php

declare(strict_types=1);

namespace App\Application\DTO;

final readonly class SaleResponseDTO
{
    /**
     * @param int $id
     * @param int $userId
     * @param float $total
     * @param string $currency
     * @param string $createdAt
     * @param array<array{id: ?int, productId: int, productName: string, quantity: int, unitPrice: float, subtotal: float}> $items
     */
    public function __construct(
        public int $id,
        public int $userId,
        public float $total,
        public string $currency,
        public string $createdAt,
        public array $items
    ) {
    }
}

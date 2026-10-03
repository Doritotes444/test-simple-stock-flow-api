<?php

declare(strict_types=1);

namespace App\Application\DTO;

final readonly class CreateSaleDTO
{
    /**
     * @param int $userId
     * @param array<SaleItemDTO> $items
     */
    public function __construct(
        public int $userId,
        public array $items
    ) {
    }
}

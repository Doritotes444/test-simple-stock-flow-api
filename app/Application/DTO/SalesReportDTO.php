<?php

declare(strict_types=1);

namespace App\Application\DTO;

final readonly class SalesReportDTO
{
    /**
     * @param int $totalTransactions
     * @param float $totalRevenue
     * @param string $currency
     * @param array<SaleResponseDTO> $sales
     */
    public function __construct(
        public int $totalTransactions,
        public float $totalRevenue,
        public string $currency,
        public array $sales
    ) {
    }
}

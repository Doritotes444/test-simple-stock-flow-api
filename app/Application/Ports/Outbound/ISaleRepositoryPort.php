<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

use App\Domain\Model\Sale;
use DateTimeImmutable;

interface ISaleRepositoryPort
{
    public function save(Sale $sale): Sale;
    public function findById(int $id): ?Sale;
    /** @return array<Sale> */
    public function findByDateRange(?DateTimeImmutable $startDate, ?DateTimeImmutable $endDate): array;
}

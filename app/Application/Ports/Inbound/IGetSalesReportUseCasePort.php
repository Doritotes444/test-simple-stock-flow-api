<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

use App\Application\DTO\SalesReportDTO;
use DateTimeImmutable;

interface IGetSalesReportUseCasePort
{
    public function execute(?DateTimeImmutable $startDate = null, ?DateTimeImmutable $endDate = null): SalesReportDTO;
}

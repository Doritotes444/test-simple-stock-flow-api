<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

use App\Application\DTO\CreateSaleDTO;
use App\Application\DTO\SaleResponseDTO;

interface IPlaceSaleUseCasePort
{
    public function execute(CreateSaleDTO $dto): SaleResponseDTO;
}

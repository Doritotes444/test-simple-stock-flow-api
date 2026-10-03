<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

use App\Application\DTO\CreateProductDTO;
use App\Application\DTO\ProductResponseDTO;

interface ICreateProductUseCasePort
{
    public function execute(CreateProductDTO $dto): ProductResponseDTO;
}

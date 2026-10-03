<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

use App\Domain\Model\Product;
use App\Domain\ValueObject\Sku;

interface IProductRepositoryPort
{
    public function findById(int $id): ?Product;
    public function findBySku(Sku $sku): ?Product;
    /** @return array<Product> */
    public function findAll(bool $onlyActive = true): array;
    public function save(Product $product): Product;
    public function update(Product $product): void;
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Ports\Outbound\IProductRepositoryPort;
use App\Domain\Model\Product;
use App\Domain\ValueObject\Sku;
use App\Infrastructure\Persistence\Eloquent\Models\ProductEloquentModel;
use App\Infrastructure\Persistence\Mappers\ProductMapper;

class EloquentProductRepository implements IProductRepositoryPort
{
    public function findById(int $id): ?Product
    {
        $model = ProductEloquentModel::find($id);
        return $model ? ProductMapper::toDomain($model) : null;
    }

    public function findBySku(Sku $sku): ?Product
    {
        $model = ProductEloquentModel::where('sku', $sku->getCode())->first();
        return $model ? ProductMapper::toDomain($model) : null;
    }

    public function findAll(bool $onlyActive = true): array
    {
        $query = ProductEloquentModel::query();
        if ($onlyActive) {
            $query->where('is_active', true);
        }

        $models = $query->orderBy('name', 'asc')->get();
        return $models->map(fn($m) => ProductMapper::toDomain($m))->all();
    }

    public function save(Product $product): Product
    {
        $model = ProductMapper::toEloquent($product);
        $model->save();
        return ProductMapper::toDomain($model);
    }

    public function update(Product $product): void
    {
        if ($product->getId()) {
            $model = ProductEloquentModel::find($product->getId());
            if ($model) {
                ProductMapper::toEloquent($product, $model);
                $model->save();
            }
        }
    }
}

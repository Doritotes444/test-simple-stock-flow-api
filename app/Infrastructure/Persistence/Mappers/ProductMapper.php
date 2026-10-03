<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers;

use App\Domain\Model\Product;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Sku;
use App\Domain\ValueObject\Stock;
use App\Infrastructure\Persistence\Eloquent\Models\ProductEloquentModel;

class ProductMapper
{
    public static function toDomain(ProductEloquentModel $model): Product
    {
        return new Product(
            id: (int) $model->id,
            sku: new Sku((string) $model->sku),
            name: (string) $model->name,
            price: new Money((float) $model->price, (string) ($model->currency ?? 'COP')),
            stock: new Stock((int) $model->stock),
            categoryId: (int) $model->category_id,
            description: $model->description,
            isActive: (bool) $model->is_active
        );
    }

    public static function toEloquent(Product $entity, ?ProductEloquentModel $model = null): ProductEloquentModel
    {
        $model = $model ?? new ProductEloquentModel();
        $model->sku = $entity->getSku()->getCode();
        $model->name = $entity->getName();
        $model->description = $entity->getDescription();
        $model->price = $entity->getPrice()->getAmount();
        $model->currency = $entity->getPrice()->getCurrency();
        $model->stock = $entity->getStock()->getUnits();
        $model->category_id = $entity->getCategoryId();
        $model->is_active = $entity->isActive();

        return $model;
    }
}

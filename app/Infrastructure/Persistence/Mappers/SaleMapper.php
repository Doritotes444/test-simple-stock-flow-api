<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers;

use App\Domain\Model\Sale;
use App\Domain\Model\SaleItem;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Quantity;
use App\Infrastructure\Persistence\Eloquent\Models\SaleEloquentModel;
use DateTimeImmutable;

class SaleMapper
{
    public static function toDomain(SaleEloquentModel $model): Sale
    {
        $items = [];
        foreach ($model->items as $itemModel) {
            $currency = $itemModel->currency ?? $model->currency ?? 'COP';
            $items[] = new SaleItem(
                id: (int) $itemModel->id,
                productId: (int) $itemModel->product_id,
                productName: (string) $itemModel->product_name,
                quantity: new Quantity((int) $itemModel->quantity),
                unitPrice: new Money((float) $itemModel->unit_price, $currency)
            );
        }

        $createdAt = $model->created_at ? DateTimeImmutable::createFromInterface($model->created_at) : new DateTimeImmutable();

        return new Sale(
            id: (int) $model->id,
            userId: (int) $model->user_id,
            items: $items,
            total: new Money((float) $model->total, (string) ($model->currency ?? 'COP')),
            createdAt: $createdAt
        );
    }
}

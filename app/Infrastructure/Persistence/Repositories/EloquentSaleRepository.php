<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Ports\Outbound\ISaleRepositoryPort;
use App\Domain\Model\Sale;
use App\Infrastructure\Persistence\Eloquent\Models\SaleEloquentModel;
use App\Infrastructure\Persistence\Eloquent\Models\SaleItemEloquentModel;
use App\Infrastructure\Persistence\Mappers\SaleMapper;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

class EloquentSaleRepository implements ISaleRepositoryPort
{
    public function save(Sale $sale): Sale
    {
        return DB::transaction(function () use ($sale) {
            $saleModel = SaleEloquentModel::create([
                'user_id' => $sale->getUserId(),
                'total' => $sale->getTotal()->getAmount(),
                'currency' => $sale->getTotal()->getCurrency(),
                'created_at' => $sale->getCreatedAt()->format('Y-m-d H:i:s'),
            ]);

            foreach ($sale->getItems() as $item) {
                SaleItemEloquentModel::create([
                    'sale_id' => $saleModel->id,
                    'product_id' => $item->getProductId(),
                    'product_name' => $item->getProductName(),
                    'quantity' => $item->getQuantity()->getValue(),
                    'unit_price' => $item->getUnitPrice()->getAmount(),
                    'subtotal' => $item->getSubtotal()->getAmount(),
                    'currency' => $item->getUnitPrice()->getCurrency(),
                ]);
            }

            $saleModel->load('items');
            return SaleMapper::toDomain($saleModel);
        });
    }

    public function findById(int $id): ?Sale
    {
        $saleModel = SaleEloquentModel::with('items')->find($id);
        return $saleModel ? SaleMapper::toDomain($saleModel) : null;
    }

    public function findByDateRange(?DateTimeImmutable $startDate, ?DateTimeImmutable $endDate): array
    {
        $query = SaleEloquentModel::with('items');

        if ($startDate) {
            $query->where('created_at', '>=', $startDate->format('Y-m-d 00:00:00'));
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate->format('Y-m-d 23:59:59'));
        }

        $models = $query->orderBy('created_at', 'desc')->get();
        return $models->map(fn($m) => SaleMapper::toDomain($m))->all();
    }
}

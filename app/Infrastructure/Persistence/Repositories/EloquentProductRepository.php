<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Ports\Outbound\ProductRepositoryInterface;
use App\Domain\Entities\Product;
use App\Domain\Exceptions\ConcurrencyConflictException;
use App\Infrastructure\Persistence\Mappers\ProductMapper;
use App\Infrastructure\Persistence\Models\ProductModel;

final class EloquentProductRepository implements ProductRepositoryInterface
{
    private array $versions = [];

    public function findById(string $id): ?Product
    {
        $model = ProductModel::withTrashed()->find($id);
        if ($model !== null) {
            $this->versions[$model->id] = (int) $model->version;
            return ProductMapper::toDomain($model);
        }
        return null;
    }

    public function findActiveById(string $id): ?Product
    {
        $model = ProductModel::query()->find($id);
        if ($model !== null) {
            $this->versions[$model->id] = (int) $model->version;
            return ProductMapper::toDomain($model);
        }
        return null;
    }

    public function searchPaginated(?string $search, ?string $categoryId, int $page, int $size): array
    {
        $query = ProductModel::query();

        if ($categoryId !== null && trim($categoryId) !== '') {
            $query->where('category_id', trim($categoryId));
        }

        if ($search !== null && trim($search) !== '') {
            $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], trim($search));
            $query->where('name', 'LIKE', '%' . $escaped . '%');
        }

        $total = $query->count();
        $models = $query->orderBy('name', 'asc')
            ->forPage($page, $size)
            ->get();

        $items = [];
        foreach ($models as $m) {
            $this->versions[$m->id] = (int) $m->version;
            $items[] = ProductMapper::toDomain($m);
        }

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    public function save(Product $product): void
    {
        $data = ProductMapper::toPersistence($product);
        $id = $product->id();

        if (!array_key_exists($id, $this->versions)) {
            // New record
            $data['version'] = 1;
            ProductModel::query()->create($data);
            $this->versions[$id] = 1;
        } else {
            // Existing record - apply optimistic locking
            $currentVersion = $this->versions[$id];
            $data['version'] = $currentVersion + 1;

            $affected = ProductModel::query()
                ->where('id', $id)
                ->where('version', $currentVersion)
                ->update($data);

            if ($affected === 0) {
                throw new ConcurrencyConflictException();
            }
            
            $this->versions[$id] = $currentVersion + 1;
        }
    }

    public function updateWithOptimisticLock(Product $product, int $expectedVersion): void
    {
        $data = ProductMapper::toPersistence($product);
        $data['version'] = $expectedVersion + 1;

        $affected = ProductModel::query()
            ->where('id', $product->id())
            ->where('version', $expectedVersion)
            ->update($data);

        if ($affected === 0) {
            throw new ConcurrencyConflictException();
        }
    }

    public function softDelete(string $id): void
    {
        ProductModel::query()->where('id', $id)->update(['image_key' => null]);
        ProductModel::query()->where('id', $id)->delete();
    }
}

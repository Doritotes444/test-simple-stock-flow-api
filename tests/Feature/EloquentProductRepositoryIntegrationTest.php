<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Entities\Product;
use App\Domain\Exceptions\ConcurrencyConflictException;
use App\Domain\ValueObjects\Money;
use App\Infrastructure\Persistence\Models\CategoryModel;
use App\Infrastructure\Persistence\Models\ProductModel;
use App\Infrastructure\Persistence\Repositories\EloquentProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentProductRepositoryIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private EloquentProductRepository $repository;
    private string $categoryId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentProductRepository();
        
        $category = CategoryModel::query()->create([
            'id' => '123e4567-e89b-12d3-a456-426614174000',
            'name' => 'Lacteos'
        ]);
        $this->categoryId = $category->id;
    }

    public function test_it_saves_and_retrieves_a_product_correctly(): void
    {
        $productId = '123e4567-e89b-12d3-a456-426614174001';
        $product = Product::create($productId, 'Leche', new Money(2500, 'COP'), 50, $this->categoryId);
        
        $this->repository->save($product);

        $this->assertDatabaseHas('products', [
            'id' => $productId,
            'name' => 'Leche',
            'stock' => 50,
            'version' => 1
        ]);

        $retrieved = $this->repository->findById($productId);
        $this->assertNotNull($retrieved);
        $this->assertEquals('Leche', $retrieved->name());
        $this->assertEquals(50, $retrieved->stock());
    }

    public function test_optimistic_locking_prevents_concurrent_updates(): void
    {
        $productId = '123e4567-e89b-12d3-a456-426614174001';
        $product = Product::create($productId, 'Leche', new Money(2500, 'COP'), 50, $this->categoryId);
        
        // Save initial version
        $this->repository->save($product);

        // Retrieve two separate instances simulating concurrent requests
        $instance1 = $this->repository->findById($productId);
        $instance2 = $this->repository->findById($productId);

        // Modify and save instance 1 (version becomes 2)
        $instance1->rename('Leche Entera');
        $this->repository->save($instance1);

        // Try to modify and save instance 2 (still expects version 1, but DB has 2)
        $this->expectException(ConcurrencyConflictException::class);
        $instance2->rename('Leche Deslactosada');
        $this->repository->save($instance2);
    }
}

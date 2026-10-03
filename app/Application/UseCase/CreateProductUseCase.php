<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\CreateProductDTO;
use App\Application\DTO\ProductResponseDTO;
use App\Application\Ports\Inbound\ICreateProductUseCasePort;
use App\Application\Ports\Outbound\IProductRepositoryPort;
use App\Domain\Exception\DomainValidationException;
use App\Domain\Model\Product;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Sku;
use App\Domain\ValueObject\Stock;

class CreateProductUseCase implements ICreateProductUseCasePort
{
    public function __construct(
        private readonly IProductRepositoryPort $productRepository
    ) {
    }

    public function execute(CreateProductDTO $dto): ProductResponseDTO
    {
        $sku = new Sku($dto->sku);
        $existing = $this->productRepository->findBySku($sku);
        if ($existing) {
            throw new DomainValidationException("Ya existe un producto registrado con el SKU '{$dto->sku}'");
        }

        $product = new Product(
            id: null,
            sku: $sku,
            name: $dto->name,
            price: new Money($dto->price),
            stock: new Stock($dto->initialStock),
            categoryId: $dto->categoryId,
            description: $dto->description,
            isActive: true
        );

        $savedProduct = $this->productRepository->save($product);

        return new ProductResponseDTO(
            id: (int) $savedProduct->getId(),
            sku: $savedProduct->getSku()->getCode(),
            name: $savedProduct->getName(),
            description: $savedProduct->getDescription(),
            price: $savedProduct->getPrice()->getAmount(),
            currency: $savedProduct->getPrice()->getCurrency(),
            stock: $savedProduct->getStock()->getUnits(),
            categoryId: $savedProduct->getCategoryId(),
            isActive: $savedProduct->isActive()
        );
    }
}

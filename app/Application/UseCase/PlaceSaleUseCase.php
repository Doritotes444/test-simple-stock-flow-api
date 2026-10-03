<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\CreateSaleDTO;
use App\Application\DTO\SaleResponseDTO;
use App\Application\Exceptions\SaleConflictException;
use App\Application\Ports\Inbound\IPlaceSaleUseCasePort;
use App\Application\Ports\Outbound\IProductRepositoryPort;
use App\Application\Ports\Outbound\ISaleRepositoryPort;
use App\Application\Ports\Outbound\IUnitOfWorkPort;
use App\Domain\Exception\DomainValidationException;
use App\Domain\Model\Sale;
use App\Domain\Model\SaleItem;
use App\Domain\Service\StockDomainService;
use App\Domain\ValueObject\Quantity;
use App\Infrastructure\Persistence\Exceptions\ProductConcurrencyException;

class PlaceSaleUseCase implements IPlaceSaleUseCasePort
{
    private const MAX_RETRIES = 3;

    public function __construct(
        private readonly ISaleRepositoryPort $saleRepository,
        private readonly IProductRepositoryPort $productRepository,
        private readonly StockDomainService $stockDomainService,
        private readonly IUnitOfWorkPort $unitOfWork
    ) {
    }

    public function execute(CreateSaleDTO $dto): SaleResponseDTO
    {
        if (empty($dto->items)) {
            throw new DomainValidationException("La venta debe contener al menos un producto");
        }

        $attempts = 0;

        while ($attempts < self::MAX_RETRIES) {
            $attempts++;
            try {
                return $this->unitOfWork->run(function () use ($dto): SaleResponseDTO {
                    $itemsToReduce = [];
                    $saleItems = [];

                    foreach ($dto->items as $itemDTO) {
                        $product = $this->productRepository->findById($itemDTO->productId);
                        if (!$product) {
                            throw new DomainValidationException("El producto con ID {$itemDTO->productId} no existe");
                        }

                        $quantity = new Quantity($itemDTO->quantity);
                        $itemsToReduce[] = [
                            'product' => $product,
                            'quantity' => $quantity,
                        ];

                        $saleItems[] = new SaleItem(
                            id: null,
                            productId: (int) $product->getId(),
                            productName: $product->getName(),
                            quantity: $quantity,
                            unitPrice: $product->getPrice()
                        );
                    }

                    // 1. Ejecutar regla de negocio de stock en Dominio
                    $this->stockDomainService->validateAndReduceStock($itemsToReduce);

                    // 2. Actualizar stock en persistencia (si hay colisión de versión lanza ProductConcurrencyException)
                    foreach ($itemsToReduce as $item) {
                        $this->productRepository->update($item['product']);
                    }

                    // 3. Crear y guardar la entidad de venta inmutable
                    $sale = new Sale(
                        id: null,
                        userId: $dto->userId,
                        items: $saleItems
                    );

                    $savedSale = $this->saleRepository->save($sale);

                    // 4. Mapear a DTO de respuesta
                    $responseItems = [];
                    foreach ($savedSale->getItems() as $item) {
                        $responseItems[] = [
                            'id' => $item->getId(),
                            'productId' => $item->getProductId(),
                            'productName' => $item->getProductName(),
                            'quantity' => $item->getQuantity()->getValue(),
                            'unitPrice' => $item->getUnitPrice()->getAmount(),
                            'subtotal' => $item->getSubtotal()->getAmount(),
                        ];
                    }

                    return new SaleResponseDTO(
                        id: (int) $savedSale->getId(),
                        userId: $savedSale->getUserId(),
                        total: (float) $savedSale->getTotal()->getAmount(),
                        currency: $savedSale->getTotal()->getCurrency(),
                        createdAt: $savedSale->getCreatedAt()->format('Y-m-d H:i:s'),
                        items: $responseItems
                    );
                });
            } catch (ProductConcurrencyException $e) {
                // Si aún quedan reintentos disponibles, continuar el bucle (CA-04.6 / E-10)
                if ($attempts >= self::MAX_RETRIES) {
                    throw new SaleConflictException(
                        "Conflicto de concurrencia: el stock del producto cambió durante la transacción tras {$attempts} intentos (HTTP 409)."
                    );
                }
                usleep(50000 * $attempts); // Backoff progresivo 50ms, 100ms
            }
        }

        throw new SaleConflictException("No se pudo completar la venta debido a colisiones concurrentes");
    }
}

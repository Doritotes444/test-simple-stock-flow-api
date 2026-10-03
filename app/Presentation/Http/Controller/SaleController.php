<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Application\DTO\CreateSaleDTO;
use App\Application\DTO\SaleItemDTO;
use App\Application\Ports\Inbound\IPlaceSaleUseCasePort;
use App\Domain\Exception\DomainValidationException;
use App\Domain\Exception\InsufficientStockException;
use App\Presentation\Http\ProblemDetails\ProblemDetailsResponse;
use App\Presentation\Http\Request\StoreSaleRequest;
use App\Presentation\Http\Resource\SaleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class SaleController extends Controller
{
    public function __construct(
        private readonly IPlaceSaleUseCasePort $placeSaleUseCase
    ) {
    }

    public function store(StoreSaleRequest $request): JsonResponse
    {
        try {
            $items = [];
            foreach ($request->input('items', []) as $item) {
                $items[] = new SaleItemDTO(
                    productId: (int) $item['product_id'],
                    quantity: (int) $item['quantity']
                );
            }

            $dto = new CreateSaleDTO(
                userId: (int) $request->input('user_id'),
                items: $items
            );

            $saleResponseDTO = $this->placeSaleUseCase->execute($dto);

            return (new SaleResource($saleResponseDTO))
                ->response()
                ->setStatusCode(201);
        } catch (InsufficientStockException $e) {
            return ProblemDetailsResponse::create(
                title: 'Stock Insuficiente',
                detail: $e->getMessage(),
                status: 422
            );
        } catch (DomainValidationException $e) {
            return ProblemDetailsResponse::create(
                title: 'Error de Validación',
                detail: $e->getMessage(),
                status: 422
            );
        }
    }
}

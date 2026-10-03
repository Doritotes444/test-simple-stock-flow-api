<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Application\DTO\CreateProductDTO;
use App\Application\DTO\ProductResponseDTO;
use App\Application\Ports\Inbound\ICreateProductUseCasePort;
use App\Application\Ports\Outbound\IProductRepositoryPort;
use App\Domain\Exception\DomainValidationException;
use App\Presentation\Http\ProblemDetails\ProblemDetailsResponse;
use App\Presentation\Http\Request\StoreProductRequest;
use App\Presentation\Http\Resource\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    public function __construct(
        private readonly ICreateProductUseCasePort $createProductUseCase,
        private readonly IProductRepositoryPort $productRepository
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $products = $this->productRepository->findAll();
        $dtos = array_map(function ($p) {
            return new ProductResponseDTO(
                id: (int) $p->getId(),
                sku: $p->getSku()->getCode(),
                name: $p->getName(),
                description: $p->getDescription(),
                price: $p->getPrice()->getAmount(),
                currency: $p->getPrice()->getCurrency(),
                stock: $p->getStock()->getUnits(),
                categoryId: $p->getCategoryId(),
                isActive: $p->isActive()
            );
        }, $products);

        return response()->json([
            'data' => ProductResource::collection($dtos),
        ]);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $dto = new CreateProductDTO(
                sku: (string) $request->input('sku'),
                name: (string) $request->input('name'),
                price: (float) $request->input('price'),
                initialStock: (int) $request->input('stock'),
                categoryId: (int) $request->input('category_id'),
                description: $request->input('description')
            );

            $productDTO = $this->createProductUseCase->execute($dto);

            return (new ProductResource($productDTO))
                ->response()
                ->setStatusCode(201);
        } catch (DomainValidationException $e) {
            return ProblemDetailsResponse::create(
                title: 'Error de Validación de Dominio',
                detail: $e->getMessage(),
                status: 422
            );
        }
    }
}

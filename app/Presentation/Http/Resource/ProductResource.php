<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resource;

use App\Application\DTO\ProductResponseDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var ProductResponseDTO $resource */
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'sku' => $resource->sku,
            'name' => $resource->name,
            'description' => $resource->description,
            'price' => $resource->price,
            'currency' => $resource->currency,
            'stock' => $resource->stock,
            'category_id' => $resource->categoryId,
            'is_active' => $resource->isActive,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resource;

use App\Application\DTO\SaleResponseDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var SaleResponseDTO $resource */
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'user_id' => $resource->userId,
            'total' => $resource->total,
            'currency' => $resource->currency,
            'created_at' => $resource->createdAt,
            'items' => $resource->items,
        ];
    }
}

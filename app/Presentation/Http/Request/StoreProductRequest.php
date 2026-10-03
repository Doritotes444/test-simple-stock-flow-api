<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'min:3', 'max:30'],
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'stock' => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
        ];
    }
}

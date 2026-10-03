<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductEloquentModel extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'sku',
        'name',
        'description',
        'price',
        'currency',
        'stock',
        'category_id',
        'is_active',
    ];

    protected $casts = [
        'price' => 'float',
        'stock' => 'integer',
        'category_id' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CategoryEloquentModel::class, 'category_id');
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItemEloquentModel::class, 'product_id');
    }
}

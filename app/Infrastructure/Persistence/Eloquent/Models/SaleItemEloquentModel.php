<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItemEloquentModel extends Model
{
    protected $table = 'sale_items';

    protected $fillable = [
        'sale_id',
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'subtotal',
        'currency',
    ];

    protected $casts = [
        'sale_id' => 'integer',
        'product_id' => 'integer',
        'quantity' => 'integer',
        'unit_price' => 'float',
        'subtotal' => 'float',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(SaleEloquentModel::class, 'sale_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductEloquentModel::class, 'product_id');
    }
}

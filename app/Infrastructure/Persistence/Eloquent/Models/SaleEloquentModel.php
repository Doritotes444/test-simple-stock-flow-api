<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaleEloquentModel extends Model
{
    protected $table = 'sales';

    protected $fillable = [
        'user_id',
        'total',
        'currency',
        'created_at',
    ];

    protected $casts = [
        'total' => 'float',
        'user_id' => 'integer',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItemEloquentModel::class, 'sale_id');
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryEloquentModel extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'description',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(ProductEloquentModel::class, 'category_id');
    }
}

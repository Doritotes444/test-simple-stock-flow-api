<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Application\Ports\Outbound\IUnitOfWorkPort;
use Closure;
use Illuminate\Support\Facades\DB;

final class LaravelUnitOfWork implements IUnitOfWorkPort
{
    public function run(Closure $operation): mixed
    {
        return DB::transaction($operation);
    }
}

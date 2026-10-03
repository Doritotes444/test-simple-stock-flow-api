<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Application\Ports\Outbound\ITransactionManagerPort;
use Closure;
use Illuminate\Support\Facades\DB;

final class LaravelTransactionManager implements ITransactionManagerPort
{
    public function execute(Closure $operation): mixed
    {
        return DB::transaction($operation);
    }
}

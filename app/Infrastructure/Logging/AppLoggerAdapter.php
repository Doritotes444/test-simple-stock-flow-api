<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use Illuminate\Support\Facades\Log;

class AppLoggerAdapter
{
    public function info(string $message, array $context = []): void
    {
        Log::info("[SimpleStockFlow] {$message}", $context);
    }

    public function error(string $message, array $context = []): void
    {
        Log::error("[SimpleStockFlow] {$message}", $context);
    }
}

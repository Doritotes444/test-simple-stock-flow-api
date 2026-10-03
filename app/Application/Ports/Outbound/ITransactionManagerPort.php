<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

use Closure;

interface ITransactionManagerPort
{
    /**
     * Ejecuta una operación atómica sin que la capa Application
     * conozca qué motor de base de datos o driver se utiliza.
     *
     * @template T
     * @param Closure(): T $operation
     * @return T
     */
    public function execute(Closure $operation): mixed;
}

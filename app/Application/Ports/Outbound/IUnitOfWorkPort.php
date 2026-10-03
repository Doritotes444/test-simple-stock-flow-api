<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

use Closure;

/**
 * Puerto UnitOfWork (Artículo II de la Constitución).
 * Permite ejecutar operaciones atómicas sin exponer la tecnología de BD.
 */
interface IUnitOfWorkPort
{
    /**
     * @template T
     * @param Closure(): T $operation
     * @return T
     */
    public function run(Closure $operation): mixed;
}

<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class AdminCreationNotAllowedException extends DomainException
{
    public function __construct(
        string $message = 'Solo se pueden dar de alta vendedores. El administrador lo crea el despliegue.'
    ) {
        parent::__construct($message);
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class CategoryNotFoundException extends DomainException
{
    public function __construct(string $categoryId)
    {
        parent::__construct(sprintf("La categoría %s no existe.", $categoryId));
    }
}

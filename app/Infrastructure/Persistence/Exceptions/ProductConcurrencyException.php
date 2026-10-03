<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Exceptions;

use RuntimeException;

class ProductConcurrencyException extends RuntimeException
{
}

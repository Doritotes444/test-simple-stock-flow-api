<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Exception\DomainValidationException;

class Category
{
    private ?int $id;
    private string $name;
    private ?string $description;

    public function __construct(?int $id, string $name, ?string $description = null)
    {
        $trimmedName = trim($name);
        if (empty($trimmedName)) {
            throw new DomainValidationException("El nombre de la categoría no puede estar vacío");
        }

        $this->id = $id;
        $this->name = $trimmedName;
        $this->description = $description ? trim($description) : null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}

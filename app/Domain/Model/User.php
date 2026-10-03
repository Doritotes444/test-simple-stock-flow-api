<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Exception\DomainValidationException;
use App\Domain\ValueObject\Email;

class User
{
    private ?int $id;
    private string $name;
    private Email $email;
    private string $passwordHash;
    private string $role;

    public function __construct(
        ?int $id,
        string $name,
        Email $email,
        string $passwordHash,
        string $role = 'CASHIER'
    ) {
        $trimmedName = trim($name);
        if (empty($trimmedName)) {
            throw new DomainValidationException("El nombre del usuario no puede estar vacío");
        }

        $validRoles = ['ADMIN', 'CASHIER', 'MANAGER'];
        $upperRole = strtoupper(trim($role));
        if (!in_array($upperRole, $validRoles, true)) {
            throw new DomainValidationException("Rol de usuario inválido: {$role}");
        }

        $this->id = $id;
        $this->name = $trimmedName;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->role = $upperRole;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getRole(): string
    {
        return $this->role;
    }
}

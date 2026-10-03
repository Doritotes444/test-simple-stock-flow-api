<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

use App\Domain\Model\User;
use App\Domain\ValueObject\Email;

interface IUserRepositoryPort
{
    public function findById(int $id): ?User;
    public function findByEmail(Email $email): ?User;
    public function save(User $user): User;
}

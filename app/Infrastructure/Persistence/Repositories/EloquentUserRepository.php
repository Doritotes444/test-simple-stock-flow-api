<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Ports\Outbound\IUserRepositoryPort;
use App\Domain\Model\User;
use App\Domain\ValueObject\Email;
use App\Infrastructure\Persistence\Eloquent\Models\UserEloquentModel;

class EloquentUserRepository implements IUserRepositoryPort
{
    public function findById(int $id): ?User
    {
        $model = UserEloquentModel::find($id);
        return $model ? $this->toDomain($model) : null;
    }

    public function findByEmail(Email $email): ?User
    {
        $model = UserEloquentModel::where('email', $email->getValue())->first();
        return $model ? $this->toDomain($model) : null;
    }

    public function save(User $user): User
    {
        $model = new UserEloquentModel();
        $model->name = $user->getName();
        $model->email = $user->getEmail()->getValue();
        $model->password = $user->getPasswordHash();
        $model->role = $user->getRole();
        $model->save();

        return $this->toDomain($model);
    }

    private function toDomain(UserEloquentModel $model): User
    {
        return new User(
            id: (int) $model->id,
            name: (string) $model->name,
            email: new Email((string) $model->email),
            passwordHash: (string) $model->password,
            role: (string) ($model->role ?? 'CASHIER')
        );
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Domain\Entities\User;
use App\Domain\Exceptions\InvalidRoleException;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    /**
     * RN-10: Username se normaliza a minÃºsculas y sin espacios
     */
    public function test_rn_10_username_is_normalized_to_lowercase_and_trimmed(): void
    {
        $user = User::create(
            'u-1',
            '  AdminUser  ',
            '$argon2id$v=19$m=65536,t=4,p=1$fakehash',
            'admin'
        );

        $this->assertSame('adminuser', $user->username());
    }

    /**
     * RN-11: El rol pertenece al conjunto cerrado: admin o seller
     */
    public function test_rn_11_role_must_be_admin_or_seller(): void
    {
        $userAdmin = User::create('u-1', 'admin1', 'hash', 'admin');
        $this->assertTrue($userAdmin->isAdmin());

        $userSeller = User::create('u-2', 'seller1', 'hash', 'seller');
        $this->assertTrue($userSeller->isSeller());

        $this->expectException(InvalidRoleException::class);
        User::create('u-3', 'invalid', 'hash', 'manager');
    }
}
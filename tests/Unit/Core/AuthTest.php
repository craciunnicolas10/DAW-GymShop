<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use App\Core\Auth;
use App\Core\Session;
use App\Models\User;
use PHPUnit\Framework\TestCase;

final class AuthTest extends TestCase
{
    private Auth $auth;

    protected function setUp(): void
    {
        $_SESSION = [];
        $this->auth = new Auth(new Session());
    }

    public function testGuestIsNotAuthenticated(): void
    {
        $this->assertFalse($this->auth->check());
        $this->assertNull($this->auth->user());
        $this->assertFalse($this->auth->isAdmin());
    }

    public function testLoginStoresUserWithoutPasswordHash(): void
    {
        $user = new User(2, 'Andrei Popescu', 'client@gymshop.test', password_hash('Client123!', PASSWORD_DEFAULT));

        $this->auth->login($user);

        $this->assertTrue($this->auth->check());
        $this->assertSame(2, $this->auth->id());
        $this->assertSame('client@gymshop.test', $this->auth->user()['email']);
        $this->assertArrayNotHasKey('passwordHash', $this->auth->user());
        $this->assertFalse($this->auth->isAdmin());
    }

    public function testAdminRoleIsRecognized(): void
    {
        $this->auth->login(new User(1, 'Admin', 'admin@gymshop.test', 'hash', User::ROLE_ADMIN));

        $this->assertTrue($this->auth->isAdmin());
        $this->assertTrue($this->auth->hasRole(User::ROLE_ADMIN));
    }

    public function testLogoutRemovesTheUser(): void
    {
        $this->auth->login(new User(2, 'Client', 'client@gymshop.test', 'hash'));
        $this->auth->logout();

        $this->assertFalse($this->auth->check());
    }

    public function testUserVerifiesHashedPassword(): void
    {
        $user = new User(2, 'Client', 'client@gymshop.test', password_hash('Client123!', PASSWORD_DEFAULT));

        $this->assertTrue($user->verifyPassword('Client123!'));
        $this->assertFalse($user->verifyPassword('wrong-password'));
    }
}

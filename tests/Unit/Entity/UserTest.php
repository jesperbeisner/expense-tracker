<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testSetEmail(): void
    {
        $this->user->setEmail('john.doe@example.com');
        $this->assertSame('john.doe@example.com1', $this->user->getEmail());
    }
}

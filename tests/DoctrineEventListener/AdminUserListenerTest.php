<?php

namespace App\Tests\DoctrineEventListener;

use App\DoctrineEventListener\AdminUserListener;
use App\Entity\AdminUser;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserListenerTest extends TestCase
{
    public function testPrePersist(): void
    {
        $adminUser = new AdminUser();
        $adminUser->setPlainPassword('abcdef');

        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $hasher->expects(self::once())
            ->method('hashPassword')
            ->with($adminUser, 'abcdef')
            ->willReturn('12345');

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $listener = new AdminUserListener($hasher);
        $listener->prePersist(new LifecycleEventArgs($adminUser, $objectManager));

        self::assertSame('12345', $adminUser->getPassword());
    }

    public function testPreUpdate(): void
    {
        $adminUser = new AdminUser();
        $adminUser->setPlainPassword('abcdef');

        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $hasher->expects(self::once())
            ->method('hashPassword')
            ->with($adminUser, 'abcdef')
            ->willReturn('12345');

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $listener = new AdminUserListener($hasher);
        $listener->prePersist(new LifecycleEventArgs($adminUser, $objectManager));

        self::assertSame('12345', $adminUser->getPassword());
    }
}
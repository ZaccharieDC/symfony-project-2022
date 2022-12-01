<?php

namespace App\DoctrineEventListener;

use App\Entity\AdminUser;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserListener
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {

    }

    public function prePersist(LifecycleEventArgs $event): void
    {
        $this->setHashedPassword($event);
    }

    public function preUpdate(LifecycleEventArgs $event): void
    {
        $this->setHashedPassword($event);
    }

    public function setHashedPassword(LifecycleEventArgs $event): void
    {
        $adminUser = $event->getObject();
        if (!$adminUser instanceof AdminUser) {
            return;
        }

        if (!empty($adminUser->getPlainPassword())) {
            $adminUser->setPassword($this->passwordHasher->hashPassword($adminUser, $adminUser->getPlainPassword()));
        }
    }
}
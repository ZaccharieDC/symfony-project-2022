<?php

namespace App\Security;

use App\Entity\AdminUser;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AdminUserVoter extends Voter
{

    public const DELETE = 'delete';

    private EntityManager $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute != self::DELETE) {
            return false;
        }

        if (!$subject instanceof AdminUser) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof AdminUser) {
            return false;
        }

        $adminUser = $subject;

        return match($attribute) {
            self::DELETE => $this->canDelete($adminUser, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canDelete(AdminUser $adminUser, AdminUser $user): bool
    {
        if ($user->getId() === $adminUser->getId()) {
            return false;
        }

        $adminUserRepository = $this->manager->getRepository(AdminUser::class);
        if ($adminUserRepository->count([]) === 1) {
            return false;
        }

        return true;
    }
}
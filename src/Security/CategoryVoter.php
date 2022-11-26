<?php

namespace App\Security;

use App\Entity\AdminUser;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CategoryVoter extends Voter
{

    public const DELETE = 'delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute != self::DELETE) {
            return false;
        }

        if (!$subject instanceof Category) {
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

        $category = $subject;

        return match($attribute) {
            self::DELETE => $this->canDelete($category),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canDelete(Category $category): bool
    {
        if ($category->getAdverts()->count() > 0) {
            return false;
        }

        return true;
    }
}
<?php

namespace App\User\Security;

use App\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserVoter extends AbstractUserAuthorizationVoter
{
    public const MODIFY_ROLES = 'modify_roles';

    public function isSubjectSupported($subject): bool
    {
        return $subject instanceof User || User::class === $subject;
    }

    protected function getActionsHandled(): array
    {
        return array_merge(
            parent::getActionsHandled(),
            [
                self::MODIFY_ROLES,
            ]
        );
    }

    public function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        if (self::CREATE === $attribute) {
            return true;
        }

        return parent::voteOnAttribute($attribute, $subject, $token);
    }

    protected function userCanModifyEntity(object $subject, User $user): bool
    {
        return $user->hasRole(User::ROLE_ADMIN) || $user === $subject;
    }

    protected function canFind($subject, User $user): bool
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    protected function canModifyRoles($subject, User $user): bool
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }
}

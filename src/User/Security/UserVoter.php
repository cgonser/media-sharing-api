<?php

namespace App\User\Security;

use App\User\Entity\User;
use App\User\Provider\UserFollowProvider;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserVoter extends AbstractUserAuthorizationVoter
{
    public function __construct(
        private UserFollowProvider $userFollowProvider,
    ) {
    }

    public function isSubjectSupported($subject): bool
    {
        return $subject instanceof User || User::class === $subject;
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
        return $user === $subject;
    }

    protected function canFind($subject, User $user): bool
    {
        return true;
    }

    /**
     * @param User $subject
     */
    protected function canRead($subject, User $user): bool
    {
        return $user->getId()->equals($subject->getId())
            || !$subject->isProfilePrivate()
            || $this->userFollowProvider->isFollowing($user->getId(), $subject->getId());
    }
}

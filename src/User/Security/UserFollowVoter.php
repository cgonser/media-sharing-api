<?php

namespace App\User\Security;

use App\User\Entity\User;
use App\User\Entity\UserFollow;
use App\User\Provider\UserFollowProvider;

class UserFollowVoter extends AbstractUserAuthorizationVoter
{
    public function __construct(
        private UserFollowProvider $userFollowProvider,
    ) {
    }

    public function isSubjectSupported($subject): bool
    {
        return $subject instanceof UserFollow || $subject === UserFollow::class;
    }

    /**
     * @param User $subject
     */
    protected function canRead($subject, User $user): bool
    {
        return !$subject->isProfilePrivate() || $this->userFollowProvider->isFollowing($user, $subject);
    }
}

<?php

namespace App\Media\Security;

use App\Media\Entity\Video;
use App\User\Entity\User;
use App\User\Provider\UserFollowProvider;
use App\User\Security\AbstractUserAuthorizationVoter;

class VideoVoter extends AbstractUserAuthorizationVoter
{
    public function __construct(
        private UserFollowProvider $userFollowProvider,
    ) {
    }

    public function isSubjectSupported($subject): bool
    {
        return $subject instanceof Video || $subject === Video::class;
    }

    /**
     * @param Video $subject
     */
    protected function canRead($subject, User $user): bool
    {
        return $user->getId()->equals($subject->getUserId())
            || !$subject->getUser()->isProfilePrivate()
            || $this->userFollowProvider->isFollowing($user->getId(), $subject->getUserId());
    }
}

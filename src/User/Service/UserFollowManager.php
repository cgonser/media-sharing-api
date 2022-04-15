<?php

namespace App\User\Service;

use App\Core\Validation\EntityValidator;
use App\User\Entity\User;
use App\User\Entity\UserFollow;
use App\User\Provider\UserFollowProvider;
use App\User\Repository\UserFollowRepository;
use Symfony\Component\Validator\Constraints as Assert;

class UserFollowManager
{
    public function __construct(
        private UserFollowRepository $userFollowRepository,
        private UserFollowProvider $userFollowProvider,
        private EntityValidator $validator
    ) {
    }

    public function follow(User $follower, User $following): UserFollow
    {
        $userFollow = new UserFollow();
        $userFollow->setFollower($follower);
        $userFollow->setFollowing($following);

        if (!$following->isProfilePrivate()) {
            $userFollow->setIsApproved(true);
        }

        $this->save($userFollow);

        return $userFollow;
    }

    public function unfollow(User $user, User $following): void
    {
        $userFollow = $this->userFollowProvider->getByFollowerAndFollowing($user->getId(), $following->getId());

        $this->delete($userFollow);
    }

    public function approve(UserFollow $userFollow): void
    {
        $userFollow->setIsApproved(true);

        $this->save($userFollow);
    }

    public function refuse(UserFollow $userFollow): void
    {
        $userFollow->setIsApproved(false);
        $this->save($userFollow);
        $this->delete($userFollow);
    }

    public function save(UserFollow $userFollow): void
    {
        $this->validator->validate($userFollow);

        $this->userFollowRepository->save($userFollow);
    }

    public function delete(?UserFollow $userFollow): void
    {
        $this->userFollowRepository->delete($userFollow);
    }
}

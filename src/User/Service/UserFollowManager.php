<?php

namespace App\User\Service;

use App\Core\Validation\EntityValidator;
use App\User\Entity\User;
use App\User\Entity\UserFollow;
use App\User\Message\UserFollowApprovedEvent;
use App\User\Message\UserFollowUnfollowedEvent;
use App\User\Provider\UserFollowProvider;
use App\User\Repository\UserFollowRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class UserFollowManager
{
    public function __construct(
        private UserFollowRepository $userFollowRepository,
        private UserFollowProvider $userFollowProvider,
        private EntityValidator $validator,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function follow(User $follower, User $following): UserFollow
    {
        $userFollow = new UserFollow();
        $userFollow->setFollower($follower);
        $userFollow->setFollowing($following);
        $this->save($userFollow);

        if (!$following->isProfilePrivate()) {
            $this->approve($userFollow);
        }

        return $userFollow;
    }

    public function unfollow(User $user, User $following): void
    {
        $userFollow = $this->userFollowProvider->getByFollowerAndFollowing($user->getId(), $following->getId());

        $this->delete($userFollow);

        $this->messageBus->dispatch(
            new UserFollowUnfollowedEvent(
                $userFollow->getFollowerId(),
                $userFollow->getFollowingId(),
            )
        );
    }

    public function approve(UserFollow $userFollow): void
    {
        $userFollow->setIsApproved(true);

        $this->save($userFollow);

        $this->messageBus->dispatch(
            new UserFollowApprovedEvent(
                $userFollow->getFollowerId(),
                $userFollow->getFollowingId(),
            )
        );
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

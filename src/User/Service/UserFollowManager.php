<?php

namespace App\User\Service;

use App\Core\Exception\InvalidEntityException;
use App\Core\Exception\InvalidInputException;
use App\Core\Validation\EntityValidator;
use App\Notification\Service\Notifier;
use App\User\Entity\User;
use App\User\Entity\UserFollow;
use App\User\Exception\UserFollowAlreadyExistsException;
use App\User\Message\UserFollowApprovedEvent;
use App\User\Message\UserFollowUnfollowedEvent;
use App\User\Notification\UserFollowApprovedNotification;
use App\User\Notification\UserFollowCreatedNotification;
use App\User\Notification\UserFollowRequestedNotification;
use App\User\Provider\UserFollowProvider;
use App\User\Repository\UserFollowRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class UserFollowManager
{
    public function __construct(
        private readonly UserFollowRepository $userFollowRepository,
        private readonly UserFollowProvider $userFollowProvider,
        private readonly EntityValidator $validator,
        private readonly MessageBusInterface $messageBus,
        private readonly Notifier $notifier,
    ) {
    }

    public function follow(User $follower, User $following): UserFollow
    {
        if ($follower->getId()->equals($following->getId())) {
            throw new InvalidInputException('You cannot follow yourself');
        }

        $userFollow = new UserFollow();
        $userFollow->setFollower($follower);
        $userFollow->setFollowing($following);

        try {
            $this->save($userFollow);
        } catch (InvalidEntityException) {
            throw new UserFollowAlreadyExistsException();
        }

        if (!$following->isProfilePrivate()) {
            $this->approve($userFollow);

            $this->notifier->send(new UserFollowCreatedNotification($userFollow), $userFollow->getFollowing());
        } else {
            $this->notifier->send(new UserFollowRequestedNotification($userFollow), $userFollow->getFollowing());
        }

        return $userFollow;
    }

    public function unfollow(User $user, User $following): void
    {
        $userFollow = $this->userFollowProvider->getByFollowerAndFollowing($user->getId(), $following->getId(), null);

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

        if ($userFollow->getFollowing()->isProfilePrivate()) {
            $this->notifier->send(new UserFollowApprovedNotification($userFollow), $userFollow->getFollower());
        }

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

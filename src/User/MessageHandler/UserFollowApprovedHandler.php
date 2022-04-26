<?php

namespace App\User\MessageHandler;

use App\User\Entity\User;
use App\User\Message\UserFollowApprovedEvent;
use App\User\Provider\UserProvider;
use App\User\Service\UserManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UserFollowApprovedHandler implements MessageHandlerInterface
{
    public function __construct(
        private UserProvider $userProvider,
        private UserManager $userManager,
    ) {
    }

    public function __invoke(UserFollowApprovedEvent $event)
    {
        /** @var User $follower */
        $follower = $this->userProvider->get($event->getFollowerId());
        $follower->setFollowingCount($follower->getFollowingCount() + 1);
        $this->userManager->save($follower);

        /** @var User $following */
        $following = $this->userProvider->get($event->getFollowingId());
        $following->setFollowersCount($following->getFollowersCount() + 1);
        $this->userManager->save($following);
    }
}

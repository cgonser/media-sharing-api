<?php

namespace App\Notification\MessageHandler;

use App\User\Entity\User;
use App\User\Message\UserCreatedEvent;
use App\User\Provider\UserProvider;
use App\Notification\Service\UserNotificationChannelManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UserCreatedHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly UserProvider $userProvider,
        private readonly UserNotificationChannelManager $userNotificationChannelManager,
    ) {
    }

    public function __invoke(UserCreatedEvent $event)
    {
        /** @var User $user */
        $user = $this->userProvider->get($event->getUserId());

        $this->userNotificationChannelManager->enableEmailNotifications($user);
    }
}

<?php

namespace App\User\MessageHandler;

use App\User\Message\UserCreatedEvent;
use App\User\Provider\UserProvider;
use App\User\Service\UserEmailManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UserCreatedHandler implements MessageHandlerInterface
{
    public function __construct(
        private UserProvider $userProvider,
        private UserEmailManager $userEmailManager,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(UserCreatedEvent $event)
    {
        $user = $this->userProvider->get($event->getUserId());

        $this->logger->info(
            'user.created',
            [
                'userId' => $event->getUserId(),
                'createdAt' => $user->getCreatedAt()->format(\DateTimeInterface::ATOM),
            ]
        );

        $this->userEmailManager->sendCreatedEmail($user);
    }
}

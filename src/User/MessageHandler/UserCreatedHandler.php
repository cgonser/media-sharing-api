<?php

namespace App\User\MessageHandler;

use App\User\Entity\User;
use App\User\Message\UserCreatedEvent;
use App\User\Provider\UserProvider;
use App\User\Service\UserEmailManager;
use App\User\Service\UserFollowManager;
use DateTimeInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UserCreatedHandler implements MessageHandlerInterface
{
    private const AUTO_FOLLOW_ACCOUNTS = [
        'moments',
    ];

    public function __construct(
        private readonly UserProvider $userProvider,
        private readonly UserEmailManager $userEmailManager,
        private readonly UserFollowManager $userFollowManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(UserCreatedEvent $event)
    {
        /** @var User $user */
        $user = $this->userProvider->get($event->getUserId());

        $this->logger->info(
            'user.created',
            [
                'userId' => $event->getUserId(),
                'createdAt' => $user->getCreatedAt()->format(DateTimeInterface::ATOM),
            ]
        );

//        $this->userEmailManager->sendCreatedEmail($user);

        $this->performAutoFollow($user);
    }

    private function performAutoFollow(User $user): void
    {
        foreach (self::AUTO_FOLLOW_ACCOUNTS as $username) {
            $following = $this->userProvider->findOneByUsername($username);

            if (null === $following) {
                continue;
            }

            $this->userFollowManager->follow($user, $following);
        }
    }
}

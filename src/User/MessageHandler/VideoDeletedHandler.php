<?php

namespace App\User\MessageHandler;

use App\Media\Message\VideoDeletedEvent;
use App\User\Entity\User;
use App\User\Provider\UserProvider;
use App\User\Service\UserManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class VideoDeletedHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly UserProvider $userProvider,
        private readonly UserManager $userManager,
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(VideoDeletedEvent $event)
    {
        /** @var User $user */
        $user = $this->userProvider->get($event->getUserId());

        $this->logger->info(
            $event::CLASS,
            [
                'videoId' => $event->getVideoId(),
                'userId' => $event->getUserId(),
            ]
        );

        $user->setVideoCount($user->getVideoCount() - 1);
        $this->userManager->update($user);
    }
}

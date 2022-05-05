<?php

namespace App\User\MessageHandler;

use App\Media\Message\VideoPublishedEvent;
use App\User\Entity\User;
use App\User\Provider\UserProvider;
use App\User\Service\UserManager;
use DateTimeInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class VideoPublishedHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly UserProvider $userProvider,
        private readonly UserManager $userManager,
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(VideoPublishedEvent $event)
    {
        /** @var User $user */
        $user = $this->userProvider->get($event->getUserId());

        $this->logger->info(
            $event::CLASS,
            [
                'videoId' => $event->getVideoId(),
                'userId' => $event->getUserId(),
                'publishedAt' => $event->getPublishedAt()->format(DateTimeInterface::ATOM),
            ]
        );

        $user->setVideoCount($user->getVideoCount() + 1);
        $this->userManager->update($user);
    }
}

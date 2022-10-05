<?php

namespace App\User\MessageHandler;

use App\Media\Message\VideoUnpublishedEvent;
use App\Media\Provider\VideoProvider;
use App\User\Entity\User;
use App\User\Provider\UserProvider;
use App\User\Service\UserManager;
use DateTimeInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class VideoUnpublishedHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly UserProvider $userProvider,
        private readonly UserManager $userManager,
        private readonly VideoProvider $videoProvider,
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(VideoUnpublishedEvent $event)
    {
        /** @var User $user */
        $user = $this->userProvider->get($event->getUserId());

        $this->logger->info(
            $event::class,
            [
                'videoId' => $event->getVideoId(),
                'userId' => $event->getUserId(),
            ]
        );

        $user->setVideoCount($this->videoProvider->countByUserId($user->getId()));
        $this->userManager->update($user);
    }
}

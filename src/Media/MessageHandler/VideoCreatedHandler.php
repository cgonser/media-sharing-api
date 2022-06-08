<?php

namespace App\Media\MessageHandler;

use App\Media\Entity\Video;
use App\Media\Message\VideoCreatedEvent;
use App\Media\Provider\VideoProvider;
use App\Media\Service\VideoMediaManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class VideoCreatedHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly VideoMediaManager $videoMediaManager,
        private readonly VideoProvider $videoProvider,
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(VideoCreatedEvent $event)
    {
        /** @var Video $video */
        $video = $this->videoProvider->get($event->getVideoId());

        $this->logger->info(
            $event::NAME,
            [
                'id' => $video->getId(),
            ]
        );

        $this->videoMediaManager->compose($video);
    }
}

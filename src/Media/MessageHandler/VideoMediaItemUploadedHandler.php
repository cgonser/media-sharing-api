<?php

namespace App\Media\MessageHandler;

use App\Media\Entity\Video;
use App\Media\Message\VideoMediaItemUploadedEvent;
use App\Media\Provider\VideoMediaItemProvider;
use App\Media\Service\VideoManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class VideoMediaItemUploadedHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly VideoMediaItemProvider $videoMediaItemProvider,
        private readonly VideoManager $videoManager,
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(VideoMediaItemUploadedEvent $event)
    {
        $videoMediaItem = $this->videoMediaItemProvider->get($event->getVideoMediaItemId());

        $this->logger->info(
            $event::NAME,
            [
                'id' => $videoMediaItem->getId(),
            ]
        );

        $video = $videoMediaItem->getVideo();

        if (Video::STATUS_PUBLISHED !== $video->getStatus()) {
            $this->videoManager->publish($video);
        }
    }
}

<?php

namespace App\Media\MessageHandler;

use App\Media\Entity\Video;
use App\Media\Entity\VideoMediaItem;
use App\Media\Enumeration\VideoStatus;
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
        /** @var VideoMediaItem $videoMediaItem */
        $videoMediaItem = $this->videoMediaItemProvider->get($event->getVideoMediaItemId());
        $video = $videoMediaItem->getVideo();

        $this->logger->info(
            $event::NAME,
            [
                'id' => $videoMediaItem->getId(),
            ]
        );

        $this->updatePublishedStatus($video);
    }

    private function updatePublishedStatus(Video $video): void
    {
        // todo: check if all required media types were generated and uploaded
        if (VideoStatus::PUBLISHED !== $video->getStatus()) {
            $this->videoManager->publish($video);
        }
    }
}

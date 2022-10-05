<?php

namespace App\Media\MessageHandler;

use App\Media\Entity\Video;
use App\Media\Entity\VideoMediaItem;
use App\Media\Enumeration\MediaItemStatus;
use App\Media\Enumeration\VideoStatus;
use App\Media\Message\VideoMediaItemUploadedEvent;
use App\Media\Provider\VideoMediaItemProvider;
use App\Media\Provider\VideoProvider;
use App\Media\Service\VideoManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class VideoMediaItemUploadedHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly VideoMediaItemProvider $videoMediaItemProvider,
        private readonly VideoProvider $videoProvider,
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
                'video_media_item.id' => $videoMediaItem->getId()->toString(),
                'media_item.id' => $videoMediaItem->getMediaItemId()->toString(),
                'video.id' => $video->getId()->toString(),
                'video.status' => $video->getStatus()->value,
            ]
        );

        if (VideoStatus::isGenerated($video->getStatus())) {
            return;
        }

        $this->updatePreviewStatus($video);
    }

    private function updatePreviewStatus(Video $video): void
    {
        $this->videoProvider->refresh($video);

        /** @var VideoMediaItem $videoMediaItem */
        foreach ($video->getVideoMediaItems() as $videoMediaItem) {
            if (MediaItemStatus::AVAILABLE !== $videoMediaItem->getMediaItem()->getStatus()) {
                return;
            }
        }

        $this->videoManager->markAsGenerated($video);
    }
}

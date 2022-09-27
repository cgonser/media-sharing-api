<?php

namespace App\Media\MessageHandler;

use App\Media\Entity\MediaItem;
use App\Media\Enumeration\MediaItemStatus;
use App\Media\Message\MediaItemUploadedEvent;
use App\Media\Message\MomentMediaItemUploadedEvent;
use App\Media\Message\VideoMediaItemUploadedEvent;
use App\Media\Provider\MediaItemProvider;
use App\Media\Provider\MomentMediaItemProvider;
use App\Media\Provider\VideoMediaItemProvider;
use App\Media\Service\MediaItemManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class MediaItemUploadedHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly MediaItemProvider $mediaItemProvider,
        private readonly MediaItemManager $mediaItemManager,
        private readonly MomentMediaItemProvider $momentMediaItemProvider,
        private readonly VideoMediaItemProvider $videoMediaItemProvider,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(MediaItemUploadedEvent $event)
    {
        $mediaItems = $this->findMediaItems($event);

        foreach ($mediaItems as $mediaItem) {
            if (MediaItemStatus::AVAILABLE === $mediaItem->getStatus()) {
                continue;
            }

            $this->mediaItemManager->refreshStatus($mediaItem);

            $this->updateReferences($mediaItem, $event->getDuration());
        }
    }

    private function findMediaItems(MediaItemUploadedEvent $event): array
    {
        if (null !== $event->getMediaItemId()) {
            return $this->mediaItemProvider->findBy(['id' => $event->getMediaItemId()]);
        }

        if (null !== $event->getAwsJobId()) {
            return $this->mediaItemProvider->findBy(['awsJobId' => $event->getAwsJobId()]);
        }

        if (null !== $event->getFilename()) {
            return $this->mediaItemProvider->findBy(['filename' => $event->getFilename()]);
        }

        return [];
    }

    private function updateReferences(MediaItem $mediaItem, ?int $duration = null): void
    {
        $momentMediaItem = $this->momentMediaItemProvider->findOneBy(['mediaItemId' => $mediaItem->getId()]);
        if (null !== $momentMediaItem) {
            $this->messageBus->dispatch(
                new MomentMediaItemUploadedEvent($momentMediaItem->getId(), $duration)
            );

            return;
        }

        $videoMediaItem = $this->videoMediaItemProvider->findOneBy(['mediaItemId' => $mediaItem->getId()]);
        if (null !== $videoMediaItem) {
            $this->messageBus->dispatch(
                new VideoMediaItemUploadedEvent($videoMediaItem->getId())
            );

            return;
        }
    }
}

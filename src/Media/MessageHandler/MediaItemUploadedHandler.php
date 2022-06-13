<?php

namespace App\Media\MessageHandler;

use App\Media\Message\MediaItemUploadedEvent;
use App\Media\Provider\MediaItemProvider;
use App\Media\Service\MediaItemManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class MediaItemUploadedHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly MediaItemProvider $mediaItemProvider,
        private readonly MediaItemManager $mediaItemManager,
    ) {
    }

    public function __invoke(MediaItemUploadedEvent $event)
    {
        $mediaItems = $this->findMediaItems($event);

        foreach ($mediaItems as $mediaItem) {
            $this->mediaItemManager->refreshStatus($mediaItem);
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
}

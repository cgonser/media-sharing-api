<?php

namespace App\Media\MessageHandler;

use App\Media\Message\MediaItemUploadedEvent;
use App\Media\Message\MomentMediaItemUploadedEvent;
use App\Media\Message\VideoMediaItemUploadedEvent;
use App\Media\Provider\MomentMediaItemProvider;
use App\Media\Provider\VideoMediaItemProvider;
use App\Media\Service\MediaItemManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class MediaItemUploadedHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly MomentMediaItemProvider $momentMediaItemProvider,
        private readonly VideoMediaItemProvider $videoMediaItemProvider,
        private readonly MediaItemManager $mediaItemManager,
        private readonly MessageBusInterface $messageBus,
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(MediaItemUploadedEvent $event)
    {
        $momentMediaItem = $this->momentMediaItemProvider->findOneBy(['mediaItemId' => $event->getMediaItemId()]);
        if (null !== $momentMediaItem) {
            $mediaItem = $momentMediaItem->getMediaItem();
            $this->mediaItemManager->updateUploadStatus($mediaItem);
            $this->messageBus->dispatch(new MomentMediaItemUploadedEvent($momentMediaItem->getId()));

            return;
        }

        $videoMediaItem = $this->videoMediaItemProvider->findOneBy(['mediaItemId' => $event->getMediaItemId()]);
        if (null !== $videoMediaItem) {
            $mediaItem = $videoMediaItem->getMediaItem();
            $this->mediaItemManager->updateUploadStatus($mediaItem);
            $this->messageBus->dispatch(new VideoMediaItemUploadedEvent($videoMediaItem->getId()));

            return;
        }

        $this->logger->warning('Media item not found', [
            'mediaItemId' => $event->getMediaItemId()->toString(),
        ]);
    }
}

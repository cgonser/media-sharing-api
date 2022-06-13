<?php

namespace App\Media\MessageHandler;

use App\Media\Message\MediaItemPublishedEvent;
use App\Media\Message\MomentMediaItemUploadedEvent;
use App\Media\Message\VideoMediaItemUploadedEvent;
use App\Media\Provider\MomentMediaItemProvider;
use App\Media\Provider\VideoMediaItemProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class MediaItemPublishedHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly MomentMediaItemProvider $momentMediaItemProvider,
        private readonly VideoMediaItemProvider $videoMediaItemProvider,
        private readonly MessageBusInterface $messageBus,
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(MediaItemPublishedEvent $event)
    {
        $momentMediaItem = $this->momentMediaItemProvider->findOneBy(['mediaItemId' => $event->getMediaItemId()]);

        if (null !== $momentMediaItem) {
            $this->messageBus->dispatch(new MomentMediaItemUploadedEvent($momentMediaItem->getId()));

            return;
        }

        $videoMediaItem = $this->videoMediaItemProvider->findOneBy(['mediaItemId' => $event->getMediaItemId()]);
        if (null !== $videoMediaItem) {
            $this->messageBus->dispatch(new VideoMediaItemUploadedEvent($videoMediaItem->getId()));

            return;
        }

        $this->logger->warning('Media item not found', [
            'event' => $event
        ]);
    }
}

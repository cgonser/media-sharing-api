<?php

namespace App\Media\MessageHandler;

use App\Media\Entity\Moment;
use App\Media\Entity\MomentMediaItem;
use App\Media\Enumeration\MediaItemStatus;
use App\Media\Enumeration\MediaItemType;
use App\Media\Enumeration\MomentStatus;
use App\Media\Message\MomentMediaItemUploadedEvent;
use App\Media\Provider\MomentMediaItemProvider;
use App\Media\Provider\MomentProvider;
use App\Media\Service\MomentManager;
use App\Media\Service\MomentMediaManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class MomentMediaItemUploadedHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly MomentProvider $momentProvider,
        private readonly MomentMediaItemProvider $momentMediaItemProvider,
        private readonly MomentMediaManager $momentMediaManager,
        private readonly MomentManager $momentManager,
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(MomentMediaItemUploadedEvent $event)
    {
        /** @var MomentMediaItem $momentMediaItem */
        $momentMediaItem = $this->momentMediaItemProvider->get($event->getMomentMediaItemId());
        $moment = $momentMediaItem->getMoment();

        $this->logger->info(
            $event::NAME,
            [
                'moment_media_item.id' => $momentMediaItem->getId()->toString(),
                'media_item.id' => $momentMediaItem->getMediaItemId()->toString(),
                'moment.id' => $moment->getId()->toString(),
                'moment.status' => $moment->getStatus()->value,
            ]
        );

        if (MomentStatus::PUBLISHED === $moment->getStatus()) {
            return;
        }

        if (MediaItemType::VIDEO_ORIGINAL === $momentMediaItem->getMediaItem()->getType()) {
            $this->momentMediaManager->convert($moment);
        }

        $this->updatePublishedStatus($moment, $event->getDuration());
    }

    private function updatePublishedStatus(Moment $moment, ?int $duration = null): void
    {
        $this->momentProvider->refresh($moment);

        /** @var MomentMediaItem $momentMediaItem */
        foreach ($moment->getMomentMediaItems() as $momentMediaItem) {
            $this->logger->info(
                'moment.media_item_uploaded.update_published_status',
                [
                    'moment.id' => $moment->getId()->toString(),
                    'media_item.id' => $momentMediaItem->getMediaItemId()->toString(),
                    'media_item.status' => $momentMediaItem->getMediaItem()->getStatus()->value,
                ]
            );

            if (MediaItemStatus::AVAILABLE !== $momentMediaItem->getMediaItem()->getStatus()) {
                return;
            }
        }

        $this->momentManager->publish($moment, $duration);
    }
}

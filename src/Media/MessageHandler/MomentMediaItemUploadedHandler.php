<?php

namespace App\Media\MessageHandler;

use App\Media\Entity\Moment;
use App\Media\Entity\MomentMediaItem;
use App\Media\Enumeration\MediaItemType;
use App\Media\Enumeration\MomentStatus;
use App\Media\Message\MomentMediaItemUploadedEvent;
use App\Media\Provider\MomentMediaItemProvider;
use App\Media\Service\MomentManager;
use App\Media\Service\MomentMediaManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class MomentMediaItemUploadedHandler implements MessageHandlerInterface
{
    public function __construct(
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
                'id' => $momentMediaItem->getId(),
            ]
        );

        if (MediaItemType::VIDEO_ORIGINAL === $momentMediaItem->getMediaItem()->getType()) {
            $this->momentMediaManager->convert($moment);
        }

        $this->updatePublishedStatus($moment);
    }

    private function updatePublishedStatus(Moment $moment): void
    {
        // todo: check if all required media types were generated and uploaded
        if (MomentStatus::PUBLISHED !== $moment->getStatus()) {
            $this->momentManager->publish($moment);
        }
    }
}

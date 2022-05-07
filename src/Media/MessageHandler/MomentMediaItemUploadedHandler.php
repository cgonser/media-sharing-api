<?php

namespace App\Media\MessageHandler;

use App\Media\Entity\Moment;
use App\Media\Message\MomentMediaItemUploadedEvent;
use App\Media\Provider\MomentMediaItemProvider;
use App\Media\Service\MomentManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class MomentMediaItemUploadedHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly MomentMediaItemProvider $momentMediaItemProvider,
        private readonly MomentManager $momentManager,
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(MomentMediaItemUploadedEvent $event)
    {
        $momentMediaItem = $this->momentMediaItemProvider->get($event->getMomentMediaItemId());

        $this->logger->info(
            $event::NAME,
            [
                'id' => $momentMediaItem->getId(),
            ]
        );

        $moment = $momentMediaItem->getMoment();

        if (Moment::STATUS_PUBLISHED !== $moment->getStatus()) {
            $this->momentManager->publish($moment);
        }
    }
}

<?php

namespace App\Media\Service;

use App\Core\Validation\EntityValidator;
use App\Media\Entity\MediaItem;
use App\Media\Entity\Video;
use App\Media\Entity\VideoMediaItem;
use App\Media\Message\VideoMediaItemUploadedEvent;
use App\Media\Repository\VideoMediaItemRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Messenger\MessageBusInterface;

class VideoMediaItemManager
{
    public function __construct(
        private VideoMediaItemRepository $videoMediaItemRepository,
        private MediaItemManager $mediaItemManager,
        private EntityValidator $validator,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function createForVideo(Video $video, string $type, string $extension): VideoMediaItem
    {
        $mediaItem = $this->mediaItemManager->createUploadableItem($type, $extension);

        $videoMediaItem = new VideoMediaItem();
        $videoMediaItem->setMediaItem($mediaItem);
        $videoMediaItem->setVideo($video);

        $this->save($videoMediaItem);

        return $videoMediaItem;
    }

    public function create(VideoMediaItem $videoMediaItem): void
    {
        $this->save($videoMediaItem);
    }

    public function update(VideoMediaItem $videoMediaItem): void
    {
        $this->save($videoMediaItem);
    }

    public function delete(object $videoMediaItem): void
    {
        $this->videoMediaItemRepository->delete($videoMediaItem);
    }

    public function save(VideoMediaItem $videoMediaItem): void
    {
        $this->validator->validate($videoMediaItem);

        $this->videoMediaItemRepository->save($videoMediaItem);
    }
}

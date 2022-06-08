<?php

namespace App\Media\Service;

use App\Core\Validation\EntityValidator;
use App\Media\Entity\MediaItem;
use App\Media\Entity\Video;
use App\Media\Entity\VideoMediaItem;
use App\Media\Enumeration\MediaItemExtension;
use App\Media\Enumeration\MediaItemType;
use App\Media\Message\VideoMediaItemUploadedEvent;
use App\Media\Repository\VideoMediaItemRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Messenger\MessageBusInterface;

class VideoMediaItemManager
{
    public function __construct(
        private readonly VideoMediaItemRepository $videoMediaItemRepository,
        private readonly MediaItemManager $mediaItemManager,
        private readonly EntityValidator $validator,
    ) {
    }

    public function createItemForVideo(
        Video $video,
        MediaItemType $type,
        MediaItemExtension $extension,
        string $filename,
    ): VideoMediaItem {
        $mediaItem = (new MediaItem())
            ->setType($type)
            ->setExtension($extension)
            ->setFilename($filename)
        ;
        $this->mediaItemManager->create($mediaItem);

        $videoMediaItem = new VideoMediaItem();
        $videoMediaItem->setMediaItem($mediaItem);
        $videoMediaItem->setVideo($video);

        $this->save($videoMediaItem);

        return $videoMediaItem;
    }

    public function createUploadableItemForVideo(
        Video $video,
        MediaItemType $type,
        MediaItemExtension $extension
    ): VideoMediaItem {
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

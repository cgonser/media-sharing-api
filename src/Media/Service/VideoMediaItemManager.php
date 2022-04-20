<?php

namespace App\Media\Service;

use App\Core\Validation\EntityValidator;
use App\Media\Entity\MediaItem;
use App\Media\Entity\Video;
use App\Media\Entity\VideoMediaItem;
use App\Media\Repository\VideoMediaItemRepository;
use Doctrine\Common\Collections\Collection;

class VideoMediaItemManager
{
    public function __construct(
        private VideoMediaItemRepository $videoMediaItemRepository,
        private MediaItemManager $mediaItemManager,
        private EntityValidator $validator,
    ) {
    }

    public function createForVideo(Video $video, string $extension): VideoMediaItem
    {
        $mediaItem = $this->mediaItemManager->createUploadableItem(MediaItem::TYPE_VIDEO, $extension);

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

    public function extractActiveMediaItems(Collection $videoMediaItems): array
    {
        $return = [];

        /** @var VideoMediaItem $videoMediaItem */
        foreach ($videoMediaItems as $videoMediaItem) {
            $mediaItem = $videoMediaItem->getMediaItem();

            if (
                MediaItem::STATUS_UPLOAD_PENDING === $mediaItem->getStatus()
                || null === $mediaItem->getPublicUrl()
            ) {
                $this->mediaItemManager->updateUploadStatus($mediaItem);

                if ($mediaItem->isDeleted()) {
                    continue;
                }
            }

            $return[] = $videoMediaItem;
        }

        return $return;
    }
}

<?php

namespace App\Media\Service;

use App\Core\Validation\EntityValidator;
use App\Media\Entity\MediaItem;
use App\Media\Entity\Moment;
use App\Media\Entity\MomentMediaItem;
use App\Media\Repository\MomentMediaItemRepository;
use Doctrine\Common\Collections\Collection;

class MomentMediaItemManager
{
    public function __construct(
        private MomentMediaItemRepository $momentMediaItemRepository,
        private MediaItemManager $mediaItemManager,
        private EntityValidator $validator,
    ) {
    }

    public function createForMoment(Moment $moment, string $extension): MomentMediaItem
    {
        $mediaItem = $this->mediaItemManager->createUploadableItem(MediaItem::TYPE_MOMENT, $extension);

        $momentMediaItem = new MomentMediaItem();
        $momentMediaItem->setMediaItem($mediaItem);
        $momentMediaItem->setMoment($moment);

        $this->save($momentMediaItem);

        return $momentMediaItem;
    }

    public function create(MomentMediaItem $momentMediaItem): void
    {
        $this->save($momentMediaItem);
    }

    public function update(MomentMediaItem $momentMediaItem): void
    {
        $this->save($momentMediaItem);
    }

    public function delete(object $momentMediaItem): void
    {
        $this->momentMediaItemRepository->delete($momentMediaItem);
    }

    public function save(MomentMediaItem $momentMediaItem): void
    {
        $this->validator->validate($momentMediaItem);

        $this->momentMediaItemRepository->save($momentMediaItem);
    }

    public function extractActiveMediaItems(Collection $momentMediaItems): array
    {
        $return = [];

        /** @var MomentMediaItem $momentMediaItem */
        foreach ($momentMediaItems as $momentMediaItem) {
            $mediaItem = $momentMediaItem->getMediaItem();

            if (MediaItem::STATUS_UPLOAD_PENDING === $mediaItem->getStatus()) {
                $this->mediaItemManager->updateUploadStatus($mediaItem);

                if ($mediaItem->isDeleted()) {
                    continue;
                }
            }

            $return[] = $momentMediaItem;
        }

        return $return;
    }
}

<?php

namespace App\Media\Service;

use App\Core\Validation\EntityValidator;
use App\Media\Entity\MediaItem;
use App\Media\Entity\Moment;
use App\Media\Entity\MomentMediaItem;
use App\Media\Repository\MomentMediaItemRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityNotFoundException;

class MomentMediaItemManager
{
    public function __construct(
        private readonly MomentMediaItemRepository $momentMediaItemRepository,
        private readonly MediaItemManager $mediaItemManager,
        private readonly EntityValidator $validator,
    ) {
    }

    public function createForMoment(Moment $moment, string $type, string $extension): MomentMediaItem
    {
        $mediaItem = $this->mediaItemManager->createUploadableItem($type, $extension);

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
            try {
                $mediaItem = $momentMediaItem->getMediaItem();

                if (
                    MediaItem::STATUS_UPLOAD_PENDING === $mediaItem->getStatus()
                    || null === $mediaItem->getPublicUrl()
                ) {
                    $this->mediaItemManager->updateUploadStatus($mediaItem);

                    if ($mediaItem->isDeleted()) {
                        $this->delete($momentMediaItem);

                        continue;
                    }
                }

                $return[] = $momentMediaItem;
            } catch (EntityNotFoundException) {
                $this->delete($momentMediaItem);
            }
        }

        return $return;
    }
}

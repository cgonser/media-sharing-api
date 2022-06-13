<?php

namespace App\Media\Service;

use App\Core\Validation\EntityValidator;
use App\Media\Entity\MediaItem;
use App\Media\Entity\Moment;
use App\Media\Entity\MomentMediaItem;
use App\Media\Enumeration\MediaItemExtension;
use App\Media\Enumeration\MediaItemStatus;
use App\Media\Enumeration\MediaItemType;
use App\Media\Exception\InvalidMediaItemExtensionException;
use App\Media\Message\MomentMediaItemUploadedEvent;
use App\Media\Provider\MomentMediaItemProvider;
use App\Media\Repository\MomentMediaItemRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class MomentMediaItemManager
{
    public function __construct(
        private readonly MomentMediaItemRepository $momentMediaItemRepository,
        private readonly MomentMediaItemProvider $momentMediaItemProvider,
        private readonly MediaItemManager $mediaItemManager,
        private readonly EntityValidator $validator,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function uploadItem(
        Moment $moment,
        MediaItemType $type,
        string $contentType,
        string $fileContents,
    ): MomentMediaItem {
        $momentMediaItem = $this->momentMediaItemProvider->findOneByMomentAndType($moment->getId(), $type);

        if (!$momentMediaItem) {
            $momentMediaItem = new MomentMediaItem();
            $momentMediaItem->setMoment($moment);
        }

        $extension = $this->mapContentTypeToExtension($contentType);

        $filename = $moment->getUserId()->toString().'/'.$moment->getId()->toString().'.'.$extension->value;

        $publicUrl = $this->mediaItemManager->uploadFile($filename, $fileContents, $contentType);

        $mediaItem = (new MediaItem())
            ->setType($type)
            ->setExtension($extension)
            ->setFilename($filename)
            ->setStatus(MediaItemStatus::AVAILABLE)
            ->setPublicUrl($publicUrl)
        ;

        $this->mediaItemManager->create($mediaItem);

        $momentMediaItem->setMediaItem($mediaItem);

        $this->save($momentMediaItem);

        $this->messageBus->dispatch(new MomentMediaItemUploadedEvent($momentMediaItem->getId()));

        return $momentMediaItem;
    }

    public function createItemForMoment(
        Moment $moment,
        MediaItemType $type,
        MediaItemExtension $extension,
        string $filename,
        ?string $awsJobId = null,
    ): MomentMediaItem {
        $momentMediaItem = $this->momentMediaItemProvider->findOneByMomentAndType($moment->getId(), $type);

        if (!$momentMediaItem) {
            $momentMediaItem = new MomentMediaItem();
            $momentMediaItem->setMoment($moment);
        }

        $mediaItem = (new MediaItem())
            ->setType($type)
            ->setExtension($extension)
            ->setFilename($filename)
            ->setAwsJobId($awsJobId)
        ;
        $this->mediaItemManager->create($mediaItem);

        $momentMediaItem->setMediaItem($mediaItem);

        $this->save($momentMediaItem);

        return $momentMediaItem;
    }

    public function createUploadableItemForMoment(
        Moment $moment,
        MediaItemType $type,
        MediaItemExtension $extension,
    ): MomentMediaItem {
        $momentMediaItem = $this->momentMediaItemProvider->findOneByMomentAndType($moment->getId(), $type);

        if (!$momentMediaItem) {
            $momentMediaItem = new MomentMediaItem();
            $momentMediaItem->setMoment($moment);
        }

        $mediaItem = $this->mediaItemManager->createUploadableItem($type, $extension);
        $momentMediaItem->setMediaItem($mediaItem);

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

    private function mapContentTypeToExtension(string $contentType): MediaItemExtension
    {
        return match ($contentType) {
            'video/quicktime' => MediaItemExtension::MOV,
            'video/mp4' => MediaItemExtension::MP4,
            'default' => throw new InvalidMediaItemExtensionException(),
        };
    }
}

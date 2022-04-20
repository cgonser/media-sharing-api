<?php

namespace App\Media\ResponseMapper;

use App\Media\Dto\MediaItemDto;
use App\Media\Dto\PublicMediaItemDto;
use App\Media\Entity\MediaItem;
use DateTimeInterface;

class MediaItemResponseMapper
{
    public function map(MediaItem $mediaItem): MediaItemDto
    {
        $mediaItemDto = new MediaItemDto();
        $mediaItemDto->id = $mediaItem->getId()->toString();
        $mediaItemDto->type = $mediaItem->getType();
        $mediaItemDto->status = $mediaItem->getStatus();
        $mediaItemDto->publicUrl = $mediaItem->getPublicUrl();
        $mediaItemDto->uploadUrl = $mediaItem->getUploadUrl();
        $mediaItemDto->uploadUrlValidUntil = $mediaItem->getUploadUrlValidUntil()?->format(\DateTimeInterface::ATOM);
        $mediaItemDto->comments = $mediaItem->getComments();
        $mediaItemDto->createdAt = $mediaItem->getCreatedAt()?->format(DateTimeInterface::ATOM);

        return $mediaItemDto;
    }

    public function mapPublic(MediaItem $mediaItem): PublicMediaItemDto
    {
        $mediaItemDto = new PublicMediaItemDto();
        $mediaItemDto->id = $mediaItem->getId()->toString();
        $mediaItemDto->type = $mediaItem->getType();
        $mediaItemDto->publicUrl = $mediaItem->getPublicUrl();

        return $mediaItemDto;
    }

    public function mapMultiple(array $mediaItems): array
    {
        return array_map(
            fn ($mediaItem) => $this->map($mediaItem),
            $mediaItems
        );
    }

    public function mapMultiplePublic(array $mediaItems): array
    {
        return array_map(
            fn ($mediaItem) => $this->mapPublic($mediaItem),
            $mediaItems
        );
    }
}

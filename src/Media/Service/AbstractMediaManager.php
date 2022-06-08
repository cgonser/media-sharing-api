<?php

namespace App\Media\Service;

use App\Media\Dto\MediaConverterOutputDto;
use App\Media\Entity\MediaItem;
use App\Media\Enumeration\MediaItemExtension;
use App\Media\Enumeration\MediaItemType;

abstract class AbstractMediaManager
{
    protected function prepareOutputVideoHigh(string $filenamePrefix): MediaConverterOutputDto
    {
        $mediaConverterOutputDto = new MediaConverterOutputDto();
        $mediaConverterOutputDto->mediaItemType = MediaItemType::VIDEO_HIGH;
        $mediaConverterOutputDto->mediaItemExtension = MediaItemExtension::MP4;
        $mediaConverterOutputDto->width = 720;
        $mediaConverterOutputDto->maxBitrate = 5000000;
        $mediaConverterOutputDto->nameModifier = MediaItemType::VIDEO_HIGH->value;
        $mediaConverterOutputDto->filename = $filenamePrefix.$mediaConverterOutputDto->nameModifier.
            '.'.MediaItemExtension::MP4->value;

        return $mediaConverterOutputDto;
    }

    protected function prepareOutputVideoLow(string $filenamePrefix): MediaConverterOutputDto
    {
        $mediaConverterOutputDto = new MediaConverterOutputDto();
        $mediaConverterOutputDto->mediaItemType = MediaItemType::VIDEO_LOW;
        $mediaConverterOutputDto->mediaItemExtension = MediaItemExtension::MP4;
        $mediaConverterOutputDto->width = 200;
        $mediaConverterOutputDto->maxBitrate = 175000;
        $mediaConverterOutputDto->nameModifier = MediaItemType::VIDEO_LOW->value;
        $mediaConverterOutputDto->filename = $filenamePrefix.$mediaConverterOutputDto->nameModifier.
            '.'.MediaItemExtension::MP4->value;

        return $mediaConverterOutputDto;
    }

    protected function prepareOutputImageThumbnail(string $filenamePrefix): MediaConverterOutputDto
    {
        $mediaConverterOutputDto = new MediaConverterOutputDto();
        $mediaConverterOutputDto->mediaItemType = MediaItemType::IMAGE_THUMBNAIL;
        $mediaConverterOutputDto->mediaItemExtension = MediaItemExtension::JPG;
        $mediaConverterOutputDto->width = 200;
        $mediaConverterOutputDto->nameModifier = MediaItemType::IMAGE_THUMBNAIL->value;
        $mediaConverterOutputDto->filename = $filenamePrefix.$mediaConverterOutputDto->nameModifier.
            '.0000000'.'.'.MediaItemExtension::JPG->value;

        return $mediaConverterOutputDto;
    }

    protected function findMediaItemByType(array $objectMediaItems, MediaItemType $mediaItemType): ?MediaItem
    {
        foreach ($objectMediaItems as $objectMediaItem) {
            if ($mediaItemType === $objectMediaItem->getMediaItem()->getType()) {
                return $objectMediaItem->getMediaItem();
            }
        }

        return null;
    }
}
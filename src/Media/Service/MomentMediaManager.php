<?php

namespace App\Media\Service;

use App\Media\Dto\MediaConverterOutputDto;
use App\Media\Entity\Moment;
use App\Media\Enumeration\MediaItemType;

class MomentMediaManager extends AbstractMediaManager
{
    public function __construct(
        private readonly AwsMediaConverterManager $awsMediaConverterManager,
        private readonly MomentMediaItemManager $momentMediaItemManager,
        private readonly string $s3BucketName,
    ) {
    }

    public function convert(Moment $moment): void
    {
        $inputs = $this->prepareInputs($moment);
        $outputGroupsThumbnail = $this->prepareOutputGroups(
            $moment,
            [
                MediaItemType::VIDEO_LOW,
                MediaItemType::IMAGE_THUMBNAIL,
            ]
        );
        $this->awsMediaConverterManager->createJob($inputs, $outputGroupsThumbnail);

        $outputGroupsHigh = $this->prepareOutputGroups(
            $moment,
            [
                MediaItemType::VIDEO_HIGH,
            ]
        );

        $this->awsMediaConverterManager->createJob($inputs, $outputGroupsHigh);
    }

    private function prepareInputs(Moment $moment): array
    {
        $mediaItem = $this->findMediaItemByType(
            $moment->getMomentMediaItems()->toArray(),
            MediaItemType::VIDEO_ORIGINAL
        );

        if (null === $mediaItem) {
            return [];
        }

        return [
            $this->awsMediaConverterManager->prepareVideoInput(
            's3://'.$this->s3BucketName.'/'.$mediaItem->getFilename()
            )
        ];
    }

    private function prepareOutputGroups(Moment $moment, array $mediaItemTypes): array
    {
        $filenamePrefix = $moment->getUserId()->toString().'/'.$moment->getId()->toString().'-';

        $mediaConverterOutputDtos = array_map(
            function(MediaItemType $mediaItemType) use ($moment, $filenamePrefix) {
                return match($mediaItemType) {
                    MediaItemType::VIDEO_HIGH => $this->prepareOutputVideoHigh($filenamePrefix),
                    MediaItemType::VIDEO_LOW => $this->prepareOutputVideoLow($filenamePrefix),
                    MediaItemType::IMAGE_THUMBNAIL => $this->prepareOutputImageThumbnail($filenamePrefix),
                };
            },
            $mediaItemTypes
        );

        foreach ($mediaConverterOutputDtos as $mediaConverterOutputDto) {
            $this->createMomentMediaItem($moment, $mediaConverterOutputDto);
        }

        return $this->awsMediaConverterManager->prepareOutputGroup(
            $mediaConverterOutputDtos,
            's3://'.$this->s3BucketName.'/'.$filenamePrefix
        );
    }

    private function createMomentMediaItem(Moment $moment, MediaConverterOutputDto $mediaConverterOutputDto): void
    {
        $this->momentMediaItemManager->createItemForMoment(
            $moment,
            $mediaConverterOutputDto->mediaItemType,
            $mediaConverterOutputDto->mediaItemExtension,
            $mediaConverterOutputDto->filename,
        );
    }
}

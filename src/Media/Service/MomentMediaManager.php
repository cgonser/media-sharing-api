<?php

namespace App\Media\Service;

use App\Media\Dto\MediaConverterOutputDto;
use App\Media\Entity\Moment;
use App\Media\Entity\MomentMediaItem;
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
        $filenamePrefix = $moment->getUserId()->toString().'/'.$moment->getId()->toString().'-';

        $this->convertToFormats(
            $moment,
            $inputs,
            $filenamePrefix,
            [
                MediaItemType::VIDEO_LOW,
                MediaItemType::IMAGE_THUMBNAIL,
            ]
        );

        $this->convertToFormats(
            $moment,
            $inputs,
            $filenamePrefix,
            [
                MediaItemType::VIDEO_HIGH,
            ]
        );
    }

    private function convertToFormats(
        Moment $moment,
        array $inputs,
        string $filenamePrefix,
        array $formats,
    ): void {
        $outputGroupsDtos = $this->prepareOutputGroups(
            $moment,
            $formats,
            $filenamePrefix
        );

        $outputGroups = $this->awsMediaConverterManager->prepareOutputGroup(
            $outputGroupsDtos,
            's3://'.$this->s3BucketName.'/'.$filenamePrefix
        );

        $awsJobId = $this->awsMediaConverterManager->createJob($inputs, $outputGroups);

        foreach ($outputGroupsDtos as $outputGroupsDto) {
            $this->createMomentMediaItem($moment, $outputGroupsDto, $awsJobId);
        }
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

    private function prepareOutputGroups(Moment $moment, array $mediaItemTypes, string $filenamePrefix): array
    {
        return array_map(
            function(MediaItemType $mediaItemType) use ($moment, $filenamePrefix) {
                return match($mediaItemType) {
                    MediaItemType::VIDEO_HIGH => $this->prepareOutputVideoHigh($filenamePrefix),
                    MediaItemType::VIDEO_LOW => $this->prepareOutputVideoLow($filenamePrefix),
                    MediaItemType::IMAGE_THUMBNAIL => $this->prepareOutputImageThumbnail($filenamePrefix),
                };
            },
            $mediaItemTypes
        );
    }

    private function createMomentMediaItem(
        Moment $moment,
        MediaConverterOutputDto $mediaConverterOutputDto,
        string $awsJobId,
    ): void {
        $this->momentMediaItemManager->createItemForMoment(
            $moment,
            $mediaConverterOutputDto->mediaItemType,
            $mediaConverterOutputDto->mediaItemExtension,
            $mediaConverterOutputDto->filename,
            $awsJobId,
        );
    }
}

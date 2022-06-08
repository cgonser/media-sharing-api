<?php

namespace App\Media\Service;

use App\Media\Dto\MediaConverterOutputDto;
use App\Media\Entity\Video;
use App\Media\Entity\VideoMoment;
use App\Media\Enumeration\MediaItemType;
use App\Media\Enumeration\MomentStatus;

class VideoMediaManager extends AbstractMediaManager
{
    public function __construct(
        private readonly AwsMediaConverterManager $awsMediaConverterManager,
        private readonly VideoMediaItemManager $videoMediaItemManager,
        private readonly string $s3BucketName,
    ) {
    }

    public function compose(Video $video): void
    {
        $inputs = $this->prepareInputs($video);
        $outputGroupsThumbnail = $this->prepareOutputGroups(
            $video,
            [
                MediaItemType::VIDEO_LOW,
                MediaItemType::IMAGE_THUMBNAIL,
            ]
        );
        $this->awsMediaConverterManager->createJob($inputs, $outputGroupsThumbnail);

        $outputGroupsHigh = $this->prepareOutputGroups(
            $video,
            [
                MediaItemType::VIDEO_HIGH,
            ]
        );

        $this->awsMediaConverterManager->createJob($inputs, $outputGroupsHigh);
//        $this->awsMediaConverterManager->createJob($inputs, $outputGroupsExport);
    }

    private function prepareInputs(Video $video): array
    {
        $inputs = [];

        /** @var VideoMoment $videoMoment */
        foreach ($video->getVideoMoments() as $videoMoment) {
            $moment = $videoMoment->getMoment();

            if (MomentStatus::PUBLISHED !== $moment->getStatus()) {
                continue;
            }

            $mediaItem = $this->findMediaItemByType(
                $moment->getMomentMediaItems()->toArray(),
                MediaItemType::VIDEO_ORIGINAL
            );

            if (null === $mediaItem) {
                continue;
            }

            $inputs[] = $this->awsMediaConverterManager->prepareVideoInput(
                's3://'.$this->s3BucketName.'/'.$mediaItem->getFilename()
            );
        }

        return $inputs;
    }

    private function prepareOutputGroups(Video $video, array $mediaItemTypes): array
    {
        $filenamePrefix = $video->getUserId()->toString().'/'.$video->getId()->toString().'-';

        $mediaConverterOutputDtos = array_map(
            function(MediaItemType $mediaItemType) use ($video, $filenamePrefix) {
                return match($mediaItemType) {
                    MediaItemType::VIDEO_HIGH => $this->prepareOutputVideoHigh($filenamePrefix),
                    MediaItemType::VIDEO_LOW => $this->prepareOutputVideoLow($filenamePrefix),
                    MediaItemType::IMAGE_THUMBNAIL => $this->prepareOutputImageThumbnail($filenamePrefix),
                };
            },
            $mediaItemTypes
        );

        foreach ($mediaConverterOutputDtos as $mediaConverterOutputDto) {
            $this->createVideoMediaItem($video, $mediaConverterOutputDto);
        }

        return $this->awsMediaConverterManager->prepareOutputGroup(
            $mediaConverterOutputDtos,
            's3://'.$this->s3BucketName.'/'.$filenamePrefix
        );
    }

    private function createVideoMediaItem(Video $video, MediaConverterOutputDto $mediaConverterOutputDto): void
    {
        $this->videoMediaItemManager->createItemForVideo(
            $video,
            $mediaConverterOutputDto->mediaItemType,
            $mediaConverterOutputDto->mediaItemExtension,
            $mediaConverterOutputDto->filename,
        );
    }
}

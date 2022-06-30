<?php

namespace App\Media\Service;

use App\Media\Dto\MediaConverterOutputDto;
use App\Media\Entity\Video;
use App\Media\Entity\VideoMoment;
use App\Media\Enumeration\MediaItemType;
use App\Media\Enumeration\MomentStatus;
use Psr\Log\LoggerInterface;

class VideoMediaManager extends AbstractMediaManager
{
    public function __construct(
        private readonly AwsMediaConverterManager $awsMediaConverterManager,
        private readonly VideoMediaItemManager $videoMediaItemManager,
        private readonly LoggerInterface $logger,
        private readonly string $s3BucketName,
    ) {
    }

    public function compose(Video $video): void
    {
        $inputs = $this->prepareInputs($video);

        $filenamePrefix = $video->getUserId()->toString().'/'.$video->getId()->toString().'-';

        $this->convertToFormats(
            $video,
            $inputs,
            $filenamePrefix,
            [
                MediaItemType::VIDEO_LOW,
                MediaItemType::IMAGE_THUMBNAIL,
            ]
        );

        $this->convertToFormats(
            $video,
            $inputs,
            $filenamePrefix,
            [
                MediaItemType::VIDEO_HIGH,
            ]
        );
    }

    private function convertToFormats(
        Video $video,
        array $inputs,
        string $filenamePrefix,
        array $formats,
    ): void {
        $outputGroupsDtos = $this->prepareOutputGroups($video, $formats, $filenamePrefix);

        $outputGroups = $this->awsMediaConverterManager->prepareOutputGroup(
            $outputGroupsDtos,
            's3://'.$this->s3BucketName.'/'.$filenamePrefix
        );

        $awsJobId = $this->awsMediaConverterManager->createJob($inputs, $outputGroups);

        $this->logger->info(
            'video.media_manager.convert_to_formats',
            [
                'video.id' => $video->getId()->toString(),
                'formats' => array_map(fn ($format) => $format->value, $formats),
                'filename_prefix' => $filenamePrefix,
                'aws_job_id' => $awsJobId,
            ]
        );

        foreach ($outputGroupsDtos as $outputGroupsDto) {
            $this->createVideoMediaItem($video, $outputGroupsDto, $awsJobId);
        }
    }

    private function prepareInputs(Video $video): array
    {
        $inputs = [];

        $audioInputFile = null !== $video->getMusic()
            ? 's3://'.$this->s3BucketName.'/'.$video->getMusic()->getFilename()
            : null;

        /** @var VideoMoment $videoMoment */
        $i = 0;
        $audioOffset = 0;
        foreach ($video->getVideoMoments() as $videoMoment) {
            $i++;
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
                's3://'.$this->s3BucketName.'/'.$mediaItem->getFilename(),
                (string) $i,
                $audioInputFile,
                $audioInputFile !== null ? $audioOffset : null,
            );

            $audioOffset += $videoMoment->getDuration();
        }

        return $inputs;
    }

    private function prepareOutputGroups(Video $video, array $mediaItemTypes, string $filenamePrefix): array
    {
        return array_map(
            function(MediaItemType $mediaItemType) use ($video, $filenamePrefix) {
                return match($mediaItemType) {
                    MediaItemType::VIDEO_HIGH => $this->prepareOutputVideoHigh($filenamePrefix),
                    MediaItemType::VIDEO_LOW => $this->prepareOutputVideoLow($filenamePrefix),
                    MediaItemType::IMAGE_THUMBNAIL => $this->prepareOutputImageThumbnail($filenamePrefix),
                };
            },
            $mediaItemTypes
        );
    }

    private function createVideoMediaItem(
        Video $video,
        MediaConverterOutputDto $mediaConverterOutputDto,
        string $awsJobId,
    ): void {
        $videoMediaItem = $this->videoMediaItemManager->createItemForVideo(
            $video,
            $mediaConverterOutputDto->mediaItemType,
            $mediaConverterOutputDto->mediaItemExtension,
            $mediaConverterOutputDto->filename,
            $awsJobId,
        );

        $this->logger->info(
            'video.media_manager.create_moment_media_item',
            [
                'moment.id' => $video->getId()->toString(),
                'video_media_item.id' => $videoMediaItem->getId()->toString(),
                'aws_job_id' => $awsJobId,
            ]
        );
    }
}

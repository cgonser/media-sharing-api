<?php

namespace App\Media\Service;

use App\Media\Dto\MediaConverterInsertableImageDto;
use App\Media\Dto\MediaConverterOutputDto;
use App\Media\Entity\Video;
use App\Media\Entity\VideoMoment;
use App\Media\Enumeration\MediaItemExtension;
use App\Media\Enumeration\MediaItemType;
use App\Media\Enumeration\MomentStatus;
use App\User\Service\UserImageManager;
use Psr\Log\LoggerInterface;

class VideoMediaManager extends AbstractMediaManager implements VideoMediaManagerInterface
{
    private const START_1_SEC_FILE = 'samples/start_1sec.mp4';
    private const START_1_SEC_DURATION = 1001;
    private const START_1_SEC_OFFSET = '00:00:01:00';
    private const START_2_SEC_FILE = 'samples/start_2sec.mp4';
    private const START_2_SEC_DURATION = 2002;
    private const START_2_SEC_OFFSET = '00:00:02:00';
    private const START_3_SEC_FILE = 'samples/start_3sec.mp4';
    private const START_3_SEC_DURATION = 2953;
    private const START_3_SEC_OFFSET = '00:00:03:00';
    private const START_4_SEC_FILE = 'samples/start_4sec.mp4';
    private const START_4_SEC_DURATION = 4004;
    private const START_4_SEC_OFFSET = '00:00:04:00';

    public function __construct(
        private readonly AwsMediaConverterManager $awsMediaConverterManager,
        private readonly VideoMediaItemManager $videoMediaItemManager,
//        private readonly MoodBarGenerator $moodBarGenerator,
        private readonly UserImageManager $userImageManager,
        private readonly LoggerInterface $logger,
        private readonly string $s3BucketName,
    ) {
    }

    public function export(Video $video): void
    {
        $filenamePrefix = $video->getUserId()->toString().'/'.$video->getId()->toString().'-';

        $this->convertToFormats(
            $video,
            $this->prepareExportInputs($video),
            $filenamePrefix,
            [
                MediaItemType::VIDEO_EXPORT,
            ]
        );
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

        $this->convertToFormats(
            $video,
            $this->prepareExportInputs($video),
            $filenamePrefix,
            [
                MediaItemType::VIDEO_EXPORT,
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

    private function prepareInputs(Video $video, int $audioOffset = 0): array
    {
        $inputs = [];

        $audioInputFile = null !== $video->getMusic()
            ? 's3://'.$this->s3BucketName.'/'.$video->getMusic()->getFilename()
            : null;

        /** @var VideoMoment $videoMoment */
        $i = 0;
        foreach ($video->getVideoMoments() as $videoMoment) {
            ++$i;
            $moment = $videoMoment->getMoment();

            if (MomentStatus::PUBLISHED !== $moment->getStatus()) {
                continue;
            }

            $mediaItem = $this->findMediaItemByType(
                $moment->getMomentMediaItems()->toArray(),
                MediaItemType::VIDEO_HIGH
            );

            if (null === $mediaItem) {
                continue;
            }

            $inputs[] = $this->awsMediaConverterManager->prepareVideoInput(
                's3://'.$this->s3BucketName.'/'.$mediaItem->getFilename(),
                '1',
                $audioInputFile,
                null !== $audioInputFile ? $audioOffset : null,
            );

            $audioOffset += $videoMoment->getDuration();
        }

        return $inputs;
    }

    private function prepareOutputGroups(Video $video, array $mediaItemTypes, string $filenamePrefix): array
    {
        return array_map(
            function (MediaItemType $mediaItemType) use ($video, $filenamePrefix) {
                return match ($mediaItemType) {
                    MediaItemType::VIDEO_EXPORT => $this->prepareOutputVideoExport(
                        $video,
                        $filenamePrefix,
                        self::START_2_SEC_OFFSET,
                    ),
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

    protected function prepareOutputVideoExport(Video $video, string $filenamePrefix, string $startOffset): MediaConverterOutputDto
    {
        $mediaConverterOutputDto = $this->prepareOutputVideoHigh($filenamePrefix);
        $mediaConverterOutputDto->mediaItemType = MediaItemType::VIDEO_EXPORT;
        $mediaConverterOutputDto->nameModifier = MediaItemType::VIDEO_EXPORT->value;
        $mediaConverterOutputDto->filename = $filenamePrefix.$mediaConverterOutputDto->nameModifier.
            '.'.MediaItemExtension::MP4->value;

        $handleInsertableImageDto = new MediaConverterInsertableImageDto();
        $handleInsertableImageDto->input = 's3://'.$this->userImageManager->getOrCreateHandleWithLogoImage($video->getUser());
        $handleInsertableImageDto->startTime = '00:00:00:00';
        $handleInsertableImageDto->width = 720;
        $handleInsertableImageDto->height = 60;
        $handleInsertableImageDto->x = 0;
        $handleInsertableImageDto->y = 130;
        $handleInsertableImageDto->layer = 99;
        $handleInsertableImageDto->opacity = 100;

//        $moodBarInsertableImageDto = new MediaConverterInsertableImageDto();
//        $moodBarInsertableImageDto->input = 's3://'.$this->moodBarGenerator->createMoodBarImage($video);
//        $moodBarInsertableImageDto->startTime = '00:00:00:00';
//        $moodBarInsertableImageDto->width = 672;
//        $moodBarInsertableImageDto->height = 5;
//        $moodBarInsertableImageDto->x = 24;
//        $moodBarInsertableImageDto->y = 110;
//        $moodBarInsertableImageDto->layer = 1;
//        $moodBarInsertableImageDto->opacity = 100;

        $mediaConverterOutputDto->insertableImages = [
            $handleInsertableImageDto,
//            $moodBarInsertableImageDto,
        ];

//        $mediaConverterOutputDto->insertableImages = array_merge(
//            $mediaConverterOutputDto->insertableImages,
//            $this->moodBarGenerator->createInsertableImages($video, self::START_2_SEC_DURATION, 2),
//        );

        return $mediaConverterOutputDto;
    }

    private function prepareExportInputs(Video $video): array
    {
        $inputs = $this->prepareInputs($video, self::START_2_SEC_DURATION);

//        $start = $this->awsMediaConverterManager->prepareVideoInput(
//            's3://'.$this->s3BucketName.'/'.self::START_2_SEC_FILE
//        );
//
//        $start['ImageInserter'] = [
//            'InsertableImages' => [
//                [
//                    'Width' => 720,
//                    'Height' => 50,
//                    'ImageX' => 0,
//                    'ImageY' => 800,
//                    'Duration' => self::START_2_SEC_DURATION,
//                    'FadeIn' => 0,
//                    'Layer' => 98,
//                    'ImageInserterInput' => 's3://'.$this->userImageManager->getOrCreateHandleImage($video->getUser()),
//                    'StartTime' => '00:00:00:00',
//                    'FadeOut' => 0,
//                    'Opacity' => 100,
//                ],
//            ],
//        ];
//
//        array_unshift($inputs, $start);

        $inputs[] = $this->awsMediaConverterManager->prepareVideoInput(
            's3://'.$this->s3BucketName.'/'.self::START_3_SEC_FILE
        );

        return $inputs;
    }
}

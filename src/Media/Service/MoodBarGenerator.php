<?php

namespace App\Media\Service;

use App\Media\Dto\MediaConverterInsertableImageDto;
use App\Media\Entity\Video;
use App\Media\Entity\VideoMoment;
use App\Media\Enumeration\Mood;
use Intervention\Image\ImageManagerStatic as Image;
use League\Flysystem\FilesystemOperator;

class MoodBarGenerator
{
    private const MARGIN_LEFT = 24;
    private const TOTAL_WIDTH = 672;
    private const IMAGE_PATH = 'moodbar/';
    private const IMAVE_OVERLAY = 'moodbar_overlay.png';
    private const FPS = 10;

    public function __construct(
        private readonly FilesystemOperator $mediaItemFilesystem,
        private readonly string $s3BucketName,
    ) {
    }

    public function createMoodBarImage(Video $video): string
    {
        return $this->s3BucketName.'/'.$this->persistMoodBarImage($video, $this->generateImageFromVideo($video));
    }

    public function persistMoodBarImage(Video $video, string $imageContents): string
    {
        $filename = self::IMAGE_PATH.$video->getId()->toString().'.png';

        $this->mediaItemFilesystem->write(
            $filename,
            $imageContents,
            [
                'Content-Type' => 'image/png',
            ],
        );

        return $filename;
    }

    public function generateImageFromVideo(Video $video): string
    {
        $moods = $this->calculateMoods($video);

        $img = Image::canvas(self::TOTAL_WIDTH, 5);

        $x = 0;
        $count = 0;
        foreach ($moods as $mood) {
            $x2 = $x + $mood['width'];
            if (++$count < count($moods)) {
                $x2 -= 10;
            }

            $img->rectangle($x, 0, $x2, 5, function ($draw) use ($mood) {
                $draw->background($mood['color']);
            });

            $x += $mood['width'];
        }

        $img->encode('png');

//        $img->save('/app/var/moodbar.png');
//        exit;

        return $img->getEncoded();
    }

    private function calculateMoods(Video $video): array
    {
        $moods = [];

        /** @var VideoMoment $videoMoment */
        foreach ($video->getVideoMoments() as $videoMoment) {
            $moods[$videoMoment->getPosition()] = [
                'duration' => $videoMoment->getMoment()->getDuration(),
                'mood' => $videoMoment->getMoment()->getMood(),
                'color' => Mood::COLORS[$videoMoment->getMoment()->getMood()->value],
                'width' => (int) floor(($videoMoment->getMoment()->getDuration() / $video->getDuration()) * self::TOTAL_WIDTH),
            ];
        }

        ksort($moods);

        return $moods;
    }

    public function createInsertableImages(Video $video, int $initialDelay = 0, int $layer = 2): array
    {
        $dtos = [];

        $moodBarInsertableImageDto = new MediaConverterInsertableImageDto();
        $moodBarInsertableImageDto->input = 's3://'.$this->s3BucketName.'/'.self::IMAGE_PATH.self::IMAVE_OVERLAY;
        $moodBarInsertableImageDto->startTime = '00:00:00:00';
        $moodBarInsertableImageDto->width = self::TOTAL_WIDTH;
        $moodBarInsertableImageDto->height = 5;
        $moodBarInsertableImageDto->x = self::MARGIN_LEFT;
        $moodBarInsertableImageDto->y = 110;
        $moodBarInsertableImageDto->layer = $layer;
        $moodBarInsertableImageDto->duration = $initialDelay;
        $moodBarInsertableImageDto->opacity = 75;

        $dtos[] = $moodBarInsertableImageDto;

        $videoDuration = $video->getDuration() + $initialDelay;

        $chunkDuration = 100;
        $chunks = floor($videoDuration / $chunkDuration);
        $chunkWidth = self::TOTAL_WIDTH / $chunks;
        $x = self::MARGIN_LEFT;

        for ($start = $initialDelay; $start < $videoDuration; $start += $chunkDuration) {
            $startSecond = floor($start / 1000);
            $startMinute = 0;
            if ($startSecond > 59) {
                $startMinute = floor($startSecond / 60);
                $startSecond = $startSecond % 60;
            }

            $startMillisecond = $start % 1000;
            $startFrame = floor( ($startMillisecond / 1000 ) * self::FPS);

            $startTime = '00:'.
                str_pad($startMinute, 2, '0', STR_PAD_LEFT).':'.
                str_pad($startSecond, 2, '0', STR_PAD_LEFT).':'.
                str_pad($startFrame, 2, '0', STR_PAD_LEFT);

            $width = floor(self::TOTAL_WIDTH + self::MARGIN_LEFT - $x + 1);

            $moodBarInsertableImageDto = new MediaConverterInsertableImageDto();
            $moodBarInsertableImageDto->input = 's3://'.$this->s3BucketName.'/'.self::IMAGE_PATH.self::IMAVE_OVERLAY;
            $moodBarInsertableImageDto->startTime = $startTime;
            $moodBarInsertableImageDto->width = $width;
            $moodBarInsertableImageDto->height = 5;
            $moodBarInsertableImageDto->x = floor($x);
            $moodBarInsertableImageDto->y = 110;
            $moodBarInsertableImageDto->layer = ++$layer;
            $moodBarInsertableImageDto->duration = $chunkDuration;
            $moodBarInsertableImageDto->opacity = 75;

            echo $layer ."\t". $startTime ."\t". floor($x) . "\t" . $width . "\t" . $chunkDuration.PHP_EOL;

            $dtos[] = $moodBarInsertableImageDto;

            $x += $chunkWidth;
        }

        return $dtos;
    }
}
<?php

namespace App\Media\Service;

use App\Media\Entity\Video;
use App\Media\Entity\VideoMoment;
use App\Media\Enumeration\Mood;
use Intervention\Image\ImageManagerStatic as Image;
use League\Flysystem\FilesystemOperator;

class MoodBarGenerator
{
    private const TOTAL_WIDTH = 672;
    private const IMAGE_PATH = 'moodbar/';

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
        foreach ($moods as $mood) {
            $img->rectangle($x, 0, $x + $mood['width'], 5, function ($draw) use ($mood) {
                $draw->background($mood['color']);
            });

            $x += $mood['width'];
        }

        $img->encode('png');

        $img->save('/app/var/moodbar.png');

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
}
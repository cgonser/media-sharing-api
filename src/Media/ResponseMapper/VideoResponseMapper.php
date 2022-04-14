<?php

namespace App\Media\ResponseMapper;

use App\Media\Dto\VideoDto;
use App\Media\Entity\Video;
use DateTimeInterface;

class VideoResponseMapper
{
    public function map(Video $video): VideoDto
    {
        $videoDto = new VideoDto();
        $videoDto->id = $video->getId()->toString();
        $videoDto->userId = $video->getUser()->getId()->toString();
        $videoDto->description = $video->getMood();
        $videoDto->mood = $video->getMood();
        $videoDto->thumbnail = $video->getThumbnail();
        $videoDto->locations = $video->getLocations();
        $videoDto->duration = $video->getDuration();
        $videoDto->recordedAt = $video->getRecordedAt()?->format(DateTimeInterface::ATOM);

        return $videoDto;
    }

    public function mapMultiple(array $videos): array
    {
        $videoDtos = [];

        foreach ($videos as $video) {
            $videoDtos[] = $this->map($video);
        }

        return $videoDtos;
    }
}

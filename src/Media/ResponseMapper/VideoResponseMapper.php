<?php

namespace App\Media\ResponseMapper;

use App\Media\Dto\VideoDto;
use App\Media\Dto\VideoMomentDto;
use App\Media\Entity\Video;
use App\Media\Entity\VideoMoment;
use DateTimeInterface;

class VideoResponseMapper
{
    public function map(Video $video): VideoDto
    {
        $videoDto = new VideoDto();
        $videoDto->id = $video->getId()->toString();
        $videoDto->userId = $video->getUser()->getId()->toString();
        $videoDto->description = $video->getDescription();
        $videoDto->mood = $video->getMood();
        $videoDto->thumbnail = $video->getThumbnail();
        $videoDto->locations = $video->getLocations();
        $videoDto->moments = $this->mapMultipleVideoMoments($videoDto, $video->getVideoMoments()->toArray());
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

    private function mapMultipleVideoMoments(VideoDto $videoDto, array $videoMoments): array
    {
        $videoMomentDtos = [];

        /** @var VideoMoment $videoMoment */
        foreach ($videoMoments as $videoMoment) {
            $videoMomentDto = new VideoMomentDto();
            $videoMomentDto->momentId = $videoMoment->getMoment()->getId();
            $videoMomentDto->position = $videoMoment->getPosition();

            $videoMomentDtos[] = $videoMomentDto;
        }

        return $videoMomentDtos;
    }
}

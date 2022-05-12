<?php

namespace App\Media\ResponseMapper;

use App\Media\Dto\PublicVideoDto;
use App\Media\Dto\VideoDto;
use App\Media\Dto\VideoMomentDto;
use App\Media\Entity\Video;
use App\Media\Entity\VideoMediaItem;
use App\Media\Entity\VideoMoment;
use App\User\ResponseMapper\UserResponseMapper;
use DateTimeInterface;
use Doctrine\ORM\EntityNotFoundException;

class VideoResponseMapper
{
    public function __construct(
        private readonly MomentResponseMapper $momentResponseMapper,
        private readonly UserResponseMapper $userResponseMapper,
    ) {
    }

    public function map(Video $video): VideoDto
    {
        $videoDto = new VideoDto();
        $this->mapBaseData($videoDto, $video);
        $videoDto->status = $video->getStatus()->value;

        return $videoDto;
    }

    public function mapPublic(Video $video): PublicVideoDto
    {
        $videoDto = new PublicVideoDto();
        $this->mapBaseData($videoDto, $video);

        return $videoDto;
    }

    public function mapMultiple(array $videos): array
    {
        return array_map(
            fn ($video) => $this->map($video),
            $videos
        );
    }

    public function mapMultiplePublic(array $videos): array
    {
        return array_map(
            fn ($video) => $this->mapPublic($video),
            $videos
        );
    }

    private function mapMediaItems(array $videoMediaItems): array
    {
        $return = [];

        /** @var VideoMediaItem $videoMediaItem */
        foreach ($videoMediaItems as $videoMediaItem) {
            $mediaItem = $videoMediaItem->getMediaItem();
            $return[$mediaItem->getType()->value] = $mediaItem->getPublicUrl();
        }

        return $return;
    }

    private function mapBaseData(VideoDto|PublicVideoDto $videoDto, Video $video): void
    {
        $videoDto->id = $video->getId()->toString();
        $videoDto->user = $this->userResponseMapper->mapPublic($video->getUser());
        $videoDto->userId = $video->getUser()->getId()->toString();
        $videoDto->description = $video->getDescription();
        $videoDto->moods = $video->getMoods();
        $videoDto->locations = $video->getLocations();
        $videoDto->moments = $this->mapVideoMoments($video->getVideoMoments()->toArray());
        $videoDto->duration = $video->getDuration();
        $videoDto->recordedAt = $video->getRecordedAt()?->format(DateTimeInterface::ATOM);
        $videoDto->mediaItems = !$video->getVideoMediaItems()->isEmpty()
            ? $this->mapMediaItems($video->getVideoMediaItems()->toArray())
            : null;
    }

    private function mapVideoMoments(array $videoMoments): array
    {
        $videoMomentDtos = [];

        /** @var VideoMoment $videoMoment */
        foreach ($videoMoments as $videoMoment) {
            $videoMomentDto = new VideoMomentDto();
            $videoMomentDto->momentId = $videoMoment->getMoment()->getId();
            $videoMomentDto->position = $videoMoment->getPosition();
            $videoMomentDto->duration = $videoMoment->getDuration();

            try {
                $videoMomentDto->moment = $this->momentResponseMapper->map($videoMoment->getMoment());
            } catch (EntityNotFoundException) {
            }

            $videoMomentDtos[] = $videoMomentDto;
        }

        return $videoMomentDtos;
    }
}

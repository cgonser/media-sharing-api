<?php

namespace App\Media\ResponseMapper;

use App\Media\Dto\PublicVideoDto;
use App\Media\Dto\VideoDto;
use App\Media\Dto\VideoMomentDto;
use App\Media\Entity\Video;
use App\Media\Entity\VideoLike;
use App\Media\Entity\VideoMediaItem;
use App\Media\Entity\VideoMoment;
use App\User\ResponseMapper\UserResponseMapper;
use DateTimeInterface;
use Doctrine\ORM\EntityNotFoundException;
use Ramsey\Uuid\UuidInterface;

class VideoResponseMapper
{
    public function __construct(
        private readonly MomentResponseMapper $momentResponseMapper,
        private readonly MusicResponseMapper $musicResponseMapper,
        private readonly LocationResponseMapper $locationResponseMapper,
        private readonly UserResponseMapper $userResponseMapper,
    ) {
    }

    public function map(Video $video, ?UuidInterface $currentUserId = null): VideoDto
    {
        $videoDto = new VideoDto();
        $this->mapBaseData($videoDto, $video, $currentUserId);
        $videoDto->status = $video->getStatus()->value;
        $videoDto->localPath = $video->getLocalPath();
        $videoDto->overrideMomentsAudio = $video->overrideMomentsAudio();

        if (null !== $video->getMusic()) {
            $videoDto->music = $this->musicResponseMapper->map($video->getMusic());
        }

        return $videoDto;
    }

    public function mapPublic(Video $video, ?UuidInterface $currentUserId = null): PublicVideoDto
    {
        $videoDto = new PublicVideoDto();
        $this->mapBaseData($videoDto, $video, $currentUserId);

        return $videoDto;
    }

    public function mapMultiple(array $videos, ?UuidInterface $currentUserId = null): array
    {
        return array_map(
            fn ($video) => $this->map($video, $currentUserId),
            $videos
        );
    }

    public function mapMultiplePublic(array $videos, ?UuidInterface $currentUserId = null): array
    {
        return array_map(
            fn ($video) => $this->mapPublic($video, $currentUserId),
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

    private function mapBaseData(VideoDto|PublicVideoDto $videoDto, Video $video, ?UuidInterface $currentUserId = null): void
    {
        $videoDto->id = $video->getId()->toString();
        $videoDto->user = $this->userResponseMapper->mapPublic($video->getUser());
        $videoDto->userId = $video->getUser()->getId()->toString();
        $videoDto->description = $video->getDescription();
        $videoDto->moods = $video->getMoods();
        $videoDto->moments = $this->mapVideoMoments($video->getVideoMoments()->toArray());
        $videoDto->duration = null !== $video->getDuration() ? round($video->getDuration() / 1000, 2) : null;
        $videoDto->likes = $video->getLikes();
        $videoDto->comments = $video->getComments();
        $videoDto->recordedAt = $video->getRecordedAt()?->format(DateTimeInterface::ATOM);
        $videoDto->mediaItems = !$video->getVideoMediaItems()->isEmpty()
            ? $this->mapMediaItems($video->getVideoMediaItems()->toArray())
            : null;

        if (null !== $currentUserId) {
            $videoDto->likedByUser = $video->getVideoLikes()->filter(
                    fn(VideoLike $videoLike) => $currentUserId->equals($videoLike->getUserId())
                )->count() > 0;
        }

        if (!$video->getVideoLocations()->isEmpty()) {
            $videoDto->locations = [];
            foreach ($video->getVideoLocations() as $videoLocation) {
                $videoDto->locations[] = $this->locationResponseMapper->map($videoLocation->getLocation());
            }
        }
    }

    private function mapVideoMoments(array $videoMoments): array
    {
        $videoMomentDtos = [];

        /** @var VideoMoment $videoMoment */
        foreach ($videoMoments as $videoMoment) {
            $videoMomentDto = new VideoMomentDto();
            $videoMomentDto->momentId = $videoMoment->getMoment()->getId();
            $videoMomentDto->position = $videoMoment->getPosition();
            $videoMomentDto->duration = round($videoMoment->getDuration() / 1000, 2);

            try {
                $videoMomentDto->moment = $this->momentResponseMapper->map($videoMoment->getMoment());
            } catch (EntityNotFoundException) {
            }

            $videoMomentDtos[] = $videoMomentDto;
        }

        return $videoMomentDtos;
    }
}

<?php

namespace App\Media\ResponseMapper;

use App\Media\Dto\VideoLikeDto;
use App\Media\Entity\VideoLike;
use App\User\ResponseMapper\UserResponseMapper;
use DateTimeInterface;

class VideoLikeResponseMapper
{
    public function __construct(
        private UserResponseMapper $userResponseMapper,
    ) {
    }

    public function map(VideoLike $videoLike): VideoLikeDto
    {
        $videoLikeDto = new VideoLikeDto();
        $videoLikeDto->id = $videoLike->getId()->toString();
        $videoLikeDto->userId = $videoLike->getUser()->getId()->toString();
        $videoLikeDto->user = $this->userResponseMapper->mapPublic($videoLike->getUser());
        $videoLikeDto->createdAt = $videoLike->getCreatedAt()?->format(DateTimeInterface::ATOM);

        return $videoLikeDto;
    }

    public function mapMultiple(array $videoLikes): array
    {
        $videoLikeDtos = [];

        foreach ($videoLikes as $videoLike) {
            $videoLikeDtos[] = $this->map($videoLike);
        }

        return $videoLikeDtos;
    }
}

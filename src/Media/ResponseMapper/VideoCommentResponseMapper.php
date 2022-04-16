<?php

namespace App\Media\ResponseMapper;

use App\Media\Dto\VideoCommentDto;
use App\Media\Entity\VideoComment;
use App\User\ResponseMapper\UserResponseMapper;
use DateTimeInterface;

class VideoCommentResponseMapper
{
    public function __construct(
        private UserResponseMapper $userResponseMapper,
    ) {
    }

    public function map(VideoComment $videoComment): VideoCommentDto
    {
        $videoCommentDto = new VideoCommentDto();
        $videoCommentDto->id = $videoComment->getId()->toString();
        $videoCommentDto->userId = $videoComment->getUser()->getId()->toString();
        $videoCommentDto->user = $this->userResponseMapper->mapPublic($videoComment->getUser());
        $videoCommentDto->comment = $videoComment->getComment();
        $videoCommentDto->createdAt = $videoComment->getCreatedAt()?->format(DateTimeInterface::ATOM);

        return $videoCommentDto;
    }

    public function mapMultiple(array $videoComments): array
    {
        $videoCommentDtos = [];

        foreach ($videoComments as $videoComment) {
            $videoCommentDtos[] = $this->map($videoComment);
        }

        return $videoCommentDtos;
    }
}

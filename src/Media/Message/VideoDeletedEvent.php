<?php

namespace App\Media\Message;

use Ramsey\Uuid\UuidInterface;

class VideoDeletedEvent
{
    public const NAME = 'media.video.deleted';

    public function __construct(
        private readonly UuidInterface $videoId,
        private readonly UuidInterface $userId,
    ) {
    }

    public function getVideoId(): UuidInterface
    {
        return $this->videoId;
    }

    public function getUserId(): UuidInterface
    {
        return $this->userId;
    }
}

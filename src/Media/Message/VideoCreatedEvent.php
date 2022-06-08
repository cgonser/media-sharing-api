<?php

namespace App\Media\Message;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

class VideoCreatedEvent
{
    public const NAME = 'media.video.created';

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

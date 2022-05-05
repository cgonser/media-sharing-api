<?php

namespace App\Media\Message;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

class VideoPublishedEvent
{
    public const NAME = 'media.video.published';

    public function __construct(
        private readonly UuidInterface $videoId,
        private readonly UuidInterface $userId,
        private readonly DateTimeInterface $publishedAt,
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

    public function getPublishedAt(): DateTimeInterface
    {
        return $this->publishedAt;
    }
}

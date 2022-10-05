<?php

namespace App\Media\Message;

use Ramsey\Uuid\UuidInterface;

class VideoUnpublishedEvent
{
    public const NAME = 'media.video.unpublished';

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

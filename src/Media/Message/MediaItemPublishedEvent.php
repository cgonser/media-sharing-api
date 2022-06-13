<?php

namespace App\Media\Message;

use Ramsey\Uuid\UuidInterface;

class MediaItemPublishedEvent
{
    public function __construct(
        private readonly UuidInterface $mediaItemId,
    ) {
    }

    public function getMediaItemId(): UuidInterface
    {
        return $this->mediaItemId;
    }
}
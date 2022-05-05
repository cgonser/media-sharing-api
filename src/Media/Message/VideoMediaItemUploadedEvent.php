<?php

namespace App\Media\Message;

use Ramsey\Uuid\UuidInterface;

class VideoMediaItemUploadedEvent
{
    public const NAME = 'media.video.uploaded';

    public function __construct(
        private readonly UuidInterface $videoMediaItemId,
    ) {
    }

    public function getVideoMediaItemId(): UuidInterface
    {
        return $this->videoMediaItemId;
    }
}

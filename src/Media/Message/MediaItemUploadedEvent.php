<?php

namespace App\Media\Message;

use Ramsey\Uuid\UuidInterface;

class MediaItemUploadedEvent
{
    public function __construct(
        private readonly UuidInterface $mediaItemId,
        private readonly string $filename,
    ) {
    }

    public function getMediaItemId(): UuidInterface
    {
        return $this->mediaItemId;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}
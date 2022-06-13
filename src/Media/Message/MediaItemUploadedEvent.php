<?php

namespace App\Media\Message;

use Ramsey\Uuid\UuidInterface;

class MediaItemUploadedEvent
{
    public function __construct(
        private readonly ?UuidInterface $mediaItemId = null,
        private readonly ?string $awsJobId = null,
        private readonly ?string $filename = null,
    ) {
    }

    public function getMediaItemId(): ?UuidInterface
    {
        return $this->mediaItemId;
    }

    public function getAwsJobId(): ?string
    {
        return $this->awsJobId;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }
}
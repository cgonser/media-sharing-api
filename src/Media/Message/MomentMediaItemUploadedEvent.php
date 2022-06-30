<?php

namespace App\Media\Message;

use Ramsey\Uuid\UuidInterface;

class MomentMediaItemUploadedEvent
{
    public const NAME = 'media.moment.uploaded';

    public function __construct(
        private readonly UuidInterface $momentMediaItemId,
        private readonly ?int $duration,
    ) {
    }

    public function getMomentMediaItemId(): UuidInterface
    {
        return $this->momentMediaItemId;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }
}

<?php

namespace App\Media\Message;

use Ramsey\Uuid\UuidInterface;

class MomentMediaItemUploadedEvent
{
    public const NAME = 'media.moment.uploaded';

    public function __construct(
        private readonly UuidInterface $momentMediaItemId,
    ) {
    }

    public function getMomentMediaItemId(): UuidInterface
    {
        return $this->momentMediaItemId;
    }
}

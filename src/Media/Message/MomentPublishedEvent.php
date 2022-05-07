<?php

namespace App\Media\Message;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

class MomentPublishedEvent
{
    public const NAME = 'media.moment.published';

    public function __construct(
        private readonly UuidInterface $momentId,
        private readonly UuidInterface $userId,
        private readonly DateTimeInterface $publishedAt,
    ) {
    }

    public function getMomentId(): UuidInterface
    {
        return $this->momentId;
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

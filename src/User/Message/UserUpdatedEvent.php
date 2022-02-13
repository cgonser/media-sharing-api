<?php

namespace App\User\Message;

use Ramsey\Uuid\UuidInterface;

class UserUpdatedEvent
{
    /**
     * @var string
     */
    public const NAME = 'user.updated';

    public function __construct(private UuidInterface $userId)
    {
    }

    public function getUserId(): ?UuidInterface
    {
        return $this->userId;
    }
}

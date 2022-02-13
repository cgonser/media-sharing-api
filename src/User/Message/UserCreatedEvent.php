<?php

namespace App\User\Message;

use Ramsey\Uuid\UuidInterface;

class UserCreatedEvent
{
    /**
     * @var string
     */
    public const NAME = 'user.created';

    public function __construct(private UuidInterface $userId)
    {
    }

    public function getUserId(): ?UuidInterface
    {
        return $this->userId;
    }
}

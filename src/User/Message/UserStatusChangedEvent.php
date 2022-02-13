<?php

namespace App\User\Message;

use Ramsey\Uuid\UuidInterface;

class UserStatusChangedEvent
{
    /**
     * @var string
     */
    public const NAME = 'user.status_changed';

    public function __construct(
        private UuidInterface $userId,
        private bool $isActive,
        private bool $isBlocked,
    ) {
    }

    public function getUserId(): ?UuidInterface
    {
        return $this->userId;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function isBlocked(): bool
    {
        return $this->isBlocked;
    }
}

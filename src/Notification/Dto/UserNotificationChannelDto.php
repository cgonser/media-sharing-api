<?php

namespace App\Notification\Dto;

use OpenApi\Attributes as OA;

class UserNotificationChannelDto
{
    public string $id;

    public string $userId;

    public string $channel;

    public ?string $deviceType = null;

    public ?string $externalId = null;

    public ?string $token = null;

    public ?string $expiresAt = null;

    #[OA\Property(type: "object")]
    public ?array $details = null;

    public string $createdAt;
}

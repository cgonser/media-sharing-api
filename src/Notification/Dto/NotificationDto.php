<?php

namespace App\Notification\Dto;

use OpenApi\Attributes as OA;
class NotificationDto
{
    public string $id;

    public string $userId;

    public string $type;

    public string $content;

    #[OA\Property(type: "object")]
    public array $context = [];

    public ?string $readAt = null;

    public string $createdAt;
}

<?php

namespace App\Notification\Request;

use App\Core\Request\AbstractRequest;
use App\Notification\Enumeration\NotificationChannel;
use DateTimeInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\RequestBody]
class UserNotificationChannelRequest extends AbstractRequest
{
    public ?string $userId;

    #[Assert\NotBlank]
    #[Assert\Type(NotificationChannel::class)]
    public ?NotificationChannel $channel;

    public ?string $device;

    public ?string $token;

    public ?string $externalId;

    #[Assert\DateTime(format: DateTimeInterface::ATOM)]
    public ?string $expiresAt;

    #[OA\Property(type: 'object')]
    public ?array $details;
}
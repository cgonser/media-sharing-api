<?php

namespace App\Notification\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Attributes as OA;

#[OA\RequestBody]
class UserNotificationChannelSearchRequest extends SearchRequest
{
    public string $userId;

    public ?string $channel = null;

    public ?string $device = null;

    public ?string $externalId = null;
}

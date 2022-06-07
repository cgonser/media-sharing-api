<?php

namespace App\Notification\Request;

use OpenApi\Attributes as OA;

#[OA\RequestBody]
class CustomPushNotificationRequest
{
    public ?string $subject = null;

    public ?string $contents = null;
}
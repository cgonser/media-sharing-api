<?php

namespace App\Tests\Api\Notification;

use App\Tests\Api\AbstractApiTest;

abstract class AbstractNotificationTest extends AbstractApiTest
{
    protected function getUserNotificationChannelDummyData(): array
    {
        return [
            'channel' => 'push',
            'device' => 'android',
            'token' => 'test-token',
            'externalId' => 'external-id',
        ];
    }
}

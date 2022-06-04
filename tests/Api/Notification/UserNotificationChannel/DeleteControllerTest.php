<?php

namespace App\Tests\Api\Notification\UserNotificationChannel;

use App\Tests\Api\Notification\AbstractNotificationTest;

class DeleteControllerTest extends AbstractNotificationTest
{
    public function testDelete(): void
    {
        $client = static::createClient();
        $userData = $this->getUserDummyData();
        $this->createUserDummy();

        $this->authenticateClient($client, $userData['email'], $userData['password']);

        $requestData = $this->getUserNotificationChannelDummyData();

        $client->jsonRequest('POST', '/users/current/notification_channels', $requestData);
        static::assertResponseStatusCodeSame('201');

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $client->jsonRequest('DELETE', '/users/current/notification_channels/'.$responseData['id']);
        static::assertResponseStatusCodeSame('204');
    }
}
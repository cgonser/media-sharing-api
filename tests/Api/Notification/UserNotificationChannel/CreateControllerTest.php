<?php

namespace App\Tests\Api\Notification\UserNotificationChannel;

use App\Tests\Api\Notification\AbstractNotificationTest;

class CreateControllerTest extends AbstractNotificationTest
{
    public function testCreate(): void
    {
        $client = static::createClient();
        $userData = $this->getUserDummyData();
        $this->createUserDummy();

        $requestData = $this->getUserNotificationChannelDummyData();
        $client->jsonRequest('POST', '/users/current/notification_channels', $requestData);
        static::assertResponseStatusCodeSame('401');

        $this->authenticateClient($client, $userData['email'], $userData['password']);

        $client->jsonRequest('POST', '/users/current/notification_channels', $requestData);

        static::assertResponseStatusCodeSame('201');
        static::assertResponseHeaderSame('Content-Type', 'application/json');

        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);
        $responseData = json_decode($responseContent, true);
        self::assertArraySubset($requestData, $responseData);
    }
}
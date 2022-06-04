<?php

namespace App\Tests\Api\Notification\UserNotificationChannel;

use App\Tests\Api\Notification\AbstractNotificationTest;

class ReadControllerTest extends AbstractNotificationTest
{
    public function testFind(): void
    {
        $client = static::createClient();
        $userData = $this->getUserDummyData();
        $this->createUserDummy();
        $this->authenticateClient($client, $userData['email'], $userData['password']);

        $requestData = $this->getUserNotificationChannelDummyData();
        $client->jsonRequest('POST', '/users/current/notification_channels', $requestData);
        static::assertResponseStatusCodeSame('201');

        $client->jsonRequest('GET', '/users/current/notification_channels');
        static::assertResponseStatusCodeSame('200');
        static::assertResponseHeaderSame('Content-Type', 'application/json');
        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);
        $responseData = json_decode($responseContent, true);

        self::assertCount(2, $responseData);

        foreach ($responseData as $userNotificationChannelResponse) {
            if ('push' === $userNotificationChannelResponse['channel']) {
                self::assertArraySubset($requestData, $userNotificationChannelResponse);
            }
        }
    }
}
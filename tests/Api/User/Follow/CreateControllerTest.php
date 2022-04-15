<?php

namespace App\Tests\Api\User\Follow;

use App\Tests\Api\User\AbstractUserTest;

class CreateControllerTest extends AbstractUserTest
{
    public function testFollowPublic(): void
    {
        $client = static::createClient();

        $user1Data = $this->getUserDummyData();
        $user1Data['email'] = 'test-user-1@itinair.com';
        $user1 = $this->createUserDummy($user1Data);

        $user2Data = $this->getUserDummyData();
        $user2Data['email'] = 'test-user-2@itinair.com';
        $user2 = $this->createUserDummy($user2Data);

        $client->jsonRequest('POST', '/users/current/follows/'.$user2->getId()->toString());
        static::assertResponseStatusCodeSame('401');

        $this->authenticateClient($client, $user1Data['email'], $user1Data['password']);
        $client->jsonRequest('POST', '/users/current/follows/'.$user2->getId()->toString());
        static::assertResponseStatusCodeSame('201');

        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);

        self::assertSame($responseData['isApproved'], true);
    }

    public function testFollowPrivate(): void
    {
        $client = static::createClient();

        $user1Data = $this->getUserDummyData();
        $user1Data['email'] = 'test-user-1@itinair.com';
        $user1 = $this->createUserDummy($user1Data);

        $user2Data = $this->getUserDummyData();
        $user2Data['email'] = 'test-user-2@itinair.com';
        $user2Data['isProfilePrivate'] = true;
        $user2 = $this->createUserDummy($user2Data);

        $client->jsonRequest('POST', '/users/current/follows/'.$user2->getId()->toString());
        static::assertResponseStatusCodeSame('401');

        $this->authenticateClient($client, $user1Data['email'], $user1Data['password']);
        $client->jsonRequest('POST', '/users/current/follows/'.$user2->getId()->toString());
        static::assertResponseStatusCodeSame('201');

        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);

        self::assertSame($responseData['isApproved'], null);
    }
}
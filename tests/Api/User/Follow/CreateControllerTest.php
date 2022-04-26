<?php

namespace App\Tests\Api\User\Follow;

use App\Tests\Api\User\AbstractUserTest;

class CreateControllerTest extends AbstractUserTest
{
    public function testFollowPublic(): void
    {
        $client = static::createClient();

        $followerData = $this->getUserDummyData();
        $followerData['email'] = 'test-user-1@itinair.com';
        $this->createUserDummy($followerData);

        $userData = $this->getUserDummyData();
        $userData['username'] = 'test-user-2';
        $userData['email'] = 'test-user-2@itinair.com';
        $user = $this->createUserDummy($userData);

        $client->jsonRequest('POST', '/users/current/follows/'.$user->getId()->toString());
        static::assertResponseStatusCodeSame('401');

        $this->authenticateClient($client, $followerData['email'], $followerData['password']);
        $client->jsonRequest('POST', '/users/current/follows/'.$user->getId()->toString());
        static::assertResponseStatusCodeSame('201');

        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);

        self::assertSame($responseData['isApproved'], true);
    }

    public function testFollowPrivate(): void
    {
        $client = static::createClient();

        $followerData = $this->getUserDummyData();
        $followerData['email'] = 'test-user-1@itinair.com';
        $this->createUserDummy($followerData);

        $userData = $this->getUserDummyData();
        $userData['username'] = 'test-user-2';
        $userData['email'] = 'test-user-2@itinair.com';
        $userData['isProfilePrivate'] = true;
        $user = $this->createUserDummy($userData);

        $client->jsonRequest('POST', '/users/current/follows/'.$user->getId()->toString());
        static::assertResponseStatusCodeSame('401');

        $this->authenticateClient($client, $followerData['email'], $followerData['password']);
        $client->jsonRequest('POST', '/users/current/follows/'.$user->getId()->toString());
        static::assertResponseStatusCodeSame('201');

        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);

        self::assertSame($responseData['isApproved'], null);
    }
}
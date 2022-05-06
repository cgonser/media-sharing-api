<?php

namespace App\Tests\Api\User;

use App\User\Service\UserFollowManager;

class ReadControllerTest extends AbstractUserTest
{
    public function testGet(): void
    {
        $client = static::createClient();

        $requestData = $this->getUserDummyData();

        $client->jsonRequest('POST', '/users', $requestData);
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);
        $userId = $responseData['id'];

        $client->jsonRequest('GET', '/users/current');
        static::assertResponseStatusCodeSame('401');

        $this->authenticateClient($client, $requestData['email'], $requestData['password']);

        $client->jsonRequest('GET', '/users/current');
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        static::assertResponseStatusCodeSame('200');
        static::assertResponseHeaderSame('Content-Type', 'application/json');

        $this->assertJson($response->getContent());
        $this->assertSame($requestData['name'], $responseData['name']);
        $this->assertSame($requestData['email'], $responseData['email']);
        $this->assertTrue($responseData['isActive']);
    }

    public function testGetCurrent(): void
    {
        $client = static::createClient();

        $requestData = $this->getUserDummyData();
        $this->createUserDummy();
        $this->authenticateClient($client, $requestData['email'], $requestData['password']);

        $client->jsonRequest('GET', '/users/current');

        static::assertResponseStatusCodeSame('200');
        static::assertResponseHeaderSame('Content-Type', 'application/json');

        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);

        $this->assertSame($requestData['name'], $responseData['name']);
        $this->assertSame($requestData['email'], $responseData['email']);
        $this->assertTrue($responseData['isActive']);
    }

    public function testGetAnotherPublic(): void
    {
        $client = static::createClient();

        $userData = $this->getUserDummyData();
        $this->createUserDummy($userData);

        $secondUserData = $this->getUserDummyData();
        $secondUserData['displayName'] = 'This is the Display Name';
        $secondUserData['username'] = 'another-test';
        $secondUserData['email'] = 'another-test@itinair.com';
        $secondUserData['phoneNumber'] = '999999999';
        $secondUser = $this->createUserDummy($secondUserData);

        $this->authenticateClient($client, $userData['email'], $userData['password']);

        $client->jsonRequest('GET', '/users/'.$secondUser->getId()->toString());
        static::assertResponseStatusCodeSame('200');

        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);

        self::assertArrayHasKey('displayName', $responseData);
        self::assertSame($secondUserData['displayName'], $responseData['displayName']);
        self::assertArrayNotHasKey('email', $responseData);
        self::assertArrayNotHasKey('phoneNumber', $responseData);
    }

    public function testGetAnotherPrivateFollowing(): void
    {
        $client = static::createClient();

        $privateUserData = $this->getUserDummyData();
        $privateUserData['displayName'] = 'This is a private profile';
        $privateUserData['isProfilePrivate'] = true;
        $privateUser = $this->createUserDummy($privateUserData);

        $followerData = $this->getUserDummyData();
        $followerData['username'] = 'another-test';
        $followerData['email'] = 'another-test@itinair.com';
        $follower = $this->createUserDummy($followerData);

        static::getContainer()->get(UserFollowManager::class)->approve(
            static::getContainer()->get(UserFollowManager::class)->follow($follower, $privateUser)
        );

        $this->authenticateClient($client, $followerData['email'], $followerData['password']);
        $client->jsonRequest('GET', '/users/'.$privateUser->getId()->toString());
        static::assertResponseStatusCodeSame('200');

        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);

        self::assertArrayHasKey('displayName', $responseData);
        self::assertSame($privateUserData['displayName'], $responseData['displayName']);
        self::assertArrayNotHasKey('email', $responseData);
        self::assertArrayNotHasKey('phoneNumber', $responseData);
    }
}

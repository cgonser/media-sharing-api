<?php

namespace App\Tests\Api\Media\Video;

use App\Tests\Api\Media\AbstractMediaTest;
use App\User\Service\UserFollowManager;

class ReadControllerTest extends AbstractMediaTest
{
    public function testSimpleGetVideo(): void
    {
        $client = static::createClient();
        $userData = $this->getUserDummyData();
        $this->createUserDummy();

        $requestData = $this->getVideoDummyData();

        $this->authenticateClient($client, $userData['email'], $userData['password']);
        $client->jsonRequest('POST', '/videos', $requestData);
        static::assertResponseStatusCodeSame('201');
        $createResponseData = json_decode($client->getResponse()->getContent(), true);

        $client->jsonRequest('GET', '/videos/'.$createResponseData['id']);
        static::assertResponseStatusCodeSame('200');
        static::assertResponseHeaderSame('Content-Type', 'application/json');
        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);

        static::assertSame($responseData, $createResponseData);
    }

    public function testGetPublicVideo(): void
    {
        $client = static::createClient();

        $user1Data = $this->getUserDummyData();
        $user1Data['email'] = 'test-user-1@itinair.com';
        $this->createUserDummy($user1Data);

        $user2Data = $this->getUserDummyData();
        $user2Data['email'] = 'test-user-2@itinair.com';
        $this->createUserDummy($user2Data);

        $createData = $this->getVideoDummyData();
        $this->authenticateClient($client, $user1Data['email'], $user1Data['password']);

        $client->jsonRequest('POST', '/videos', $createData);
        static::assertResponseStatusCodeSame('201');
        $createResponseData = json_decode($client->getResponse()->getContent(), true);

        $client->jsonRequest('GET', '/videos/'.$createResponseData['id']);
        static::assertResponseStatusCodeSame('200');

        $this->authenticateClient($client, $user2Data['email'], $user2Data['password']);
        $client->jsonRequest('GET', '/videos/'.$createResponseData['id']);
        static::assertResponseStatusCodeSame('200');
    }

    public function testGetPrivateVideoNotFollowing(): void
    {
        $client = static::createClient();

        $user1Data = $this->getUserDummyData();
        $user1Data['email'] = 'test-user-1@itinair.com';
        $user1Data['isProfilePrivate'] = true;
        $this->createUserDummy($user1Data);

        $user2Data = $this->getUserDummyData();
        $user2Data['email'] = 'test-user-2@itinair.com';
        $this->createUserDummy($user2Data);

        $createData = $this->getVideoDummyData();
        $this->authenticateClient($client, $user1Data['email'], $user1Data['password']);

        $client->jsonRequest('POST', '/videos', $createData);
        static::assertResponseStatusCodeSame('201');
        $createResponseData = json_decode($client->getResponse()->getContent(), true);

        $client->jsonRequest('GET', '/videos/'.$createResponseData['id']);
        static::assertResponseStatusCodeSame('200');

        $this->authenticateClient($client, $user2Data['email'], $user2Data['password']);
        $client->jsonRequest('GET', '/videos/'.$createResponseData['id']);
        static::assertResponseStatusCodeSame('403');
    }

    public function testGetPrivateVideoFollowing(): void
    {
        $client = static::createClient();

        $userData = $this->getUserDummyData();
        $userData['email'] = 'test-user-1@itinair.com';
        $userData['isProfilePrivate'] = true;
        $user = $this->createUserDummy($userData);

        $followerData = $this->getUserDummyData();
        $followerData['email'] = 'test-user-2@itinair.com';
        $follower = $this->createUserDummy($followerData);

        static::getContainer()->get(UserFollowManager::class)->approve(
            static::getContainer()->get(UserFollowManager::class)->follow($follower, $user)
        );

        $createData = $this->getVideoDummyData();
        $this->authenticateClient($client, $userData['email'], $userData['password']);

        $client->jsonRequest('POST', '/videos', $createData);
        static::assertResponseStatusCodeSame('201');
        $createResponseData = json_decode($client->getResponse()->getContent(), true);

        $client->jsonRequest('GET', '/videos/'.$createResponseData['id']);
        static::assertResponseStatusCodeSame('200');

        $this->authenticateClient($client, $followerData['email'], $followerData['password']);
        $client->jsonRequest('GET', '/videos/'.$createResponseData['id']);
        static::assertResponseStatusCodeSame('200');
    }

    public function testFind(): void
    {
        $client = static::createClient();

        $userData = $this->getUserDummyData();
        $userData['email'] = 'test-user-1@itinair.com';
        $userData['isProfilePrivate'] = true;
        $user = $this->createUserDummy($userData);

        $followerData = $this->getUserDummyData();
        $followerData['email'] = 'test-user-2@itinair.com';
        $follower = $this->createUserDummy($followerData);

        $nonFollowerData = $this->getUserDummyData();
        $nonFollowerData['email'] = 'test-user-3@itinair.com';
        $nonFollower = $this->createUserDummy($nonFollowerData);

        static::getContainer()->get(UserFollowManager::class)->approve(
            static::getContainer()->get(UserFollowManager::class)->follow($follower, $user)
        );

        $createData = $this->getVideoDummyData();
        $this->authenticateClient($client, $userData['email'], $userData['password']);

        $client->jsonRequest('POST', '/videos', $createData);
        static::assertResponseStatusCodeSame('201');
        $createResponseData = json_decode($client->getResponse()->getContent(), true);

        $this->authenticateClient($client, $followerData['email'], $followerData['password']);
        $client->jsonRequest('GET', '/videos');
        static::assertResponseStatusCodeSame('200');
        $findResponseData = json_decode($client->getResponse()->getContent(), true);
        static::assertCount(1, $findResponseData);

        $this->authenticateClient($client, $nonFollowerData['email'], $nonFollowerData['password']);
        $client->jsonRequest('GET', '/videos');
        static::assertResponseStatusCodeSame('200');
        $findResponseData = json_decode($client->getResponse()->getContent(), true);
        static::assertCount(0, $findResponseData);
    }
}

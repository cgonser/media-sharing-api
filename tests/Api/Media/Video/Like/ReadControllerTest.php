<?php

namespace App\Tests\Api\Media\Video\Like;

use App\Tests\Api\Media\AbstractMediaTest;
use App\User\Service\UserFollowManager;

class ReadControllerTest extends AbstractMediaTest
{
    public function testReadLikeFromPublicVideo(): void
    {
        $client = static::createClient();
        $userData = $this->getUserDummyData();
        $this->createUserDummy($userData);
        $this->authenticateClient($client, $userData['email'], $userData['password']);

        $client->jsonRequest('POST', '/videos', $this->getVideoDummyData());
        $videoResponseData = json_decode($client->getResponse()->getContent(), true);

        $nonFollowerData = $this->getUserDummyData();
        $nonFollowerData['email'] = 'test-user-1@itinair.com';
        $nonFollowerData['username'] = 'test-user-1';
        $this->createUserDummy($nonFollowerData);
        $this->authenticateClient($client, $nonFollowerData['email'], $nonFollowerData['password']);

        $client->jsonRequest('PUT', '/videos/'.$videoResponseData['id'].'/likes');
        $client->jsonRequest('GET', '/videos/'.$videoResponseData['id'].'/likes');
        static::assertResponseStatusCodeSame('200');
        static::assertResponseHeaderSame('Content-Type', 'application/json');
        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $videoLikesResponseData = json_decode($response->getContent(), true);

        static::assertSame(count($videoLikesResponseData), 1);
    }

    public function testReadLikeFromPrivateVideoNonFollower(): void
    {
        $client = static::createClient();
        $userData = $this->getUserDummyData();
        $userData['isProfilePrivate'] = true;
        $this->createUserDummy($userData);
        $this->authenticateClient($client, $userData['email'], $userData['password']);

        $client->jsonRequest('POST', '/videos', $this->getVideoDummyData());
        $videoResponseData = json_decode($client->getResponse()->getContent(), true);

        $nonFollowerData = $this->getUserDummyData();
        $nonFollowerData['email'] = 'test-user-1@itinair.com';
        $nonFollowerData['username'] = 'test-user-1';
        $this->createUserDummy($nonFollowerData);
        $this->authenticateClient($client, $nonFollowerData['email'], $nonFollowerData['password']);

        $client->jsonRequest('GET', '/videos/'.$videoResponseData['id'].'/likes');
        static::assertResponseStatusCodeSame('403');
    }

    public function testReadLikeFromPrivateVideoFollower(): void
    {
        $client = static::createClient();
        $userData = $this->getUserDummyData();
        $userData['isProfilePrivate'] = true;
        $user = $this->createUserDummy($userData);

        $followerData = $this->getUserDummyData();
        $followerData['email'] = 'test-user-1@itinair.com';
        $followerData['username'] = 'test-user-1';
        $follower = $this->createUserDummy($followerData);

        static::getContainer()->get(UserFollowManager::class)->approve(
            static::getContainer()->get(UserFollowManager::class)->follow($follower, $user)
        );

        $this->authenticateClient($client, $userData['email'], $userData['password']);
        $client->jsonRequest('POST', '/videos', $this->getVideoDummyData());
        $videoResponseData = json_decode($client->getResponse()->getContent(), true);

        $this->authenticateClient($client, $followerData['email'], $followerData['password']);
        $client->jsonRequest('PUT', '/videos/'.$videoResponseData['id'].'/likes');
        $client->jsonRequest('GET', '/videos/'.$videoResponseData['id'].'/likes');
        static::assertResponseStatusCodeSame('200');
        static::assertResponseHeaderSame('Content-Type', 'application/json');
        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $videoLikesResponseData = json_decode($response->getContent(), true);
        static::assertSame(count($videoLikesResponseData), 1);
    }
}

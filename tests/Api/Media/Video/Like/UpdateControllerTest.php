<?php

namespace App\Tests\Api\Media\Video\Like;

use App\Tests\Api\Media\AbstractMediaTest;
use App\User\Service\UserFollowManager;

class UpdateControllerTest extends AbstractMediaTest
{
    public function testLikePublicVideo(): void
    {
        $client = static::createClient();
        $userData = $this->getUserDummyData();
        $this->createUserDummy($userData);
        $this->authenticateClient($client, $userData['email'], $userData['password']);

        $client->jsonRequest('POST', '/videos', $this->getVideoDummyData());
        $videoResponseData = json_decode($client->getResponse()->getContent(), true);

        $nonFollowerData = $this->getUserDummyData();
        $nonFollowerData['email'] = 'test-user-1@itinair.com';
        $this->createUserDummy($nonFollowerData);
        $this->authenticateClient($client, $nonFollowerData['email'], $nonFollowerData['password']);

        $client->jsonRequest('PUT', '/videos/'.$videoResponseData['id'].'/likes');
        static::assertResponseStatusCodeSame('204');

        $client->jsonRequest('PUT', '/videos/'.$videoResponseData['id'].'/likes');
        static::assertResponseStatusCodeSame('400');
    }

    public function testLikePrivateVideoNonFollower(): void
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
        $this->createUserDummy($nonFollowerData);
        $this->authenticateClient($client, $nonFollowerData['email'], $nonFollowerData['password']);

        $client->jsonRequest('PUT', '/videos/'.$videoResponseData['id'].'/likes');
        static::assertResponseStatusCodeSame('403');
    }

    public function testLikePrivateVideoFollower(): void
    {
        $client = static::createClient();
        $userData = $this->getUserDummyData();
        $userData['isProfilePrivate'] = true;
        $user = $this->createUserDummy($userData);

        $followerData = $this->getUserDummyData();
        $followerData['email'] = 'test-user-1@itinair.com';
        $follower = $this->createUserDummy($followerData);

        static::getContainer()->get(UserFollowManager::class)->approve(
            static::getContainer()->get(UserFollowManager::class)->follow($follower, $user)
        );

        $this->authenticateClient($client, $userData['email'], $userData['password']);
        $client->jsonRequest('POST', '/videos', $this->getVideoDummyData());
        $videoResponseData = json_decode($client->getResponse()->getContent(), true);

        $this->authenticateClient($client, $followerData['email'], $followerData['password']);
        $client->jsonRequest('PUT', '/videos/'.$videoResponseData['id'].'/likes');
        static::assertResponseStatusCodeSame('204');
    }
}

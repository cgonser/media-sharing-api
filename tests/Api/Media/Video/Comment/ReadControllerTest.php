<?php

namespace App\Tests\Api\Media\Video\Comment;

use App\Tests\Api\Media\AbstractMediaTest;
use App\User\Service\UserFollowManager;

class ReadControllerTest extends AbstractMediaTest
{
    public function testReadCommentFromPublicVideo(): void
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

        $client->jsonRequest('POST', '/videos/'.$videoResponseData['id'].'/comments', ['comment' => 'my comment']);
        $client->jsonRequest('POST', '/videos/'.$videoResponseData['id'].'/comments', ['comment' => 'my comment']);
        $client->jsonRequest('POST', '/videos/'.$videoResponseData['id'].'/comments', ['comment' => 'my comment']);

        $client->jsonRequest('GET', '/videos/'.$videoResponseData['id'].'/comments');
        static::assertResponseStatusCodeSame('200');
        static::assertResponseHeaderSame('Content-Type', 'application/json');
        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $videoCommentsResponseData = json_decode($response->getContent(), true);

        static::assertSame(count($videoCommentsResponseData), 3);
    }

    public function testReadCommentFromPrivateVideoNonFollower(): void
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

        $client->jsonRequest('GET', '/videos/'.$videoResponseData['id'].'/comments');
        static::assertResponseStatusCodeSame('403');
    }

    public function testReadCommentFromPrivateVideoFollower(): void
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
        $client->jsonRequest('POST', '/videos/'.$videoResponseData['id'].'/comments', ['comment' => 'my comment']);
        $client->jsonRequest('GET', '/videos/'.$videoResponseData['id'].'/comments');
        static::assertResponseStatusCodeSame('200');
        static::assertResponseHeaderSame('Content-Type', 'application/json');
        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $videoCommentsResponseData = json_decode($response->getContent(), true);
        static::assertSame(count($videoCommentsResponseData), 1);
    }
}

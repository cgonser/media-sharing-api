<?php

namespace App\Tests\Api\Media\Video\Comment;

use App\Tests\Api\Media\AbstractMediaTest;
use App\User\Service\UserFollowManager;

class UpdateControllerTest extends AbstractMediaTest
{
    public function testUpdateComment(): void
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
        static::assertResponseHeaderSame('Content-Type', 'application/json');
        $videoCommentResponseData = json_decode($client->getResponse()->getContent(), true);

        $client->jsonRequest(
            'PATCH',
            '/videos/'.$videoResponseData['id'].'/comments/'.$videoCommentResponseData['id'],
            ['comment' => 'my updated comment']
        );
        static::assertResponseStatusCodeSame('200');
        static::assertResponseHeaderSame('Content-Type', 'application/json');
        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $videoCommentResponseData = json_decode($response->getContent(), true);

        static::assertSame($videoCommentResponseData['comment'], 'my updated comment');
    }
}

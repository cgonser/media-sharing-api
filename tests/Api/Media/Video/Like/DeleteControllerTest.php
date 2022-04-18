<?php

namespace App\Tests\Api\Media\Video\Like;

use App\Tests\Api\Media\AbstractMediaTest;

class DeleteControllerTest extends AbstractMediaTest
{
    public function testUnlikePublicVideo(): void
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

        $client->jsonRequest('DELETE', '/videos/'.$videoResponseData['id'].'/likes');
        static::assertResponseStatusCodeSame('404');

        $client->jsonRequest('PUT', '/videos/'.$videoResponseData['id'].'/likes');
        static::assertResponseStatusCodeSame('204');

        $client->jsonRequest('DELETE', '/videos/'.$videoResponseData['id'].'/likes');
        static::assertResponseStatusCodeSame('204');

        $client->jsonRequest('DELETE', '/videos/'.$videoResponseData['id'].'/likes');
        static::assertResponseStatusCodeSame('404');
    }
}

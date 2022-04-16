<?php

namespace App\Tests\Api\Media\Video;

use App\Tests\Api\Media\AbstractMediaTest;

class DeleteControllerTest extends AbstractMediaTest
{
    public function testDeleteVideo(): void
    {
        $client = static::createClient();
        $userData = $this->getUserDummyData();
        $this->createUserDummy();
        $this->authenticateClient($client, $userData['email'], $userData['password']);

        $requestData = $this->getVideoDummyData();
        $client->jsonRequest('POST', '/videos', $requestData);
        static::assertResponseStatusCodeSame('201');
        static::assertResponseHeaderSame('Content-Type', 'application/json');

        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);

        $client->jsonRequest('DELETE', '/videos/'.$responseData['id']);
        static::assertResponseStatusCodeSame('204');

        $client->jsonRequest('GET', '/videos/'.$responseData['id']);
        static::assertResponseStatusCodeSame('404');
    }
}

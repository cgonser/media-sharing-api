<?php

namespace App\Tests\Api\Media\Video;

use App\Tests\Api\Media\AbstractMediaTest;

class UpdateControllerTest extends AbstractMediaTest
{
    public function testUpdateVideo(): void
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

        $updateRequest = [
            'description' => 'This is a new description',
            'mood' => 'joy',
            'locations' => ['London'],
            'duration' => 12,
        ];

        $client->jsonRequest('PATCH', '/videos/'.$responseData['id'], $updateRequest);
        static::assertResponseStatusCodeSame('200');
        static::assertResponseHeaderSame('Content-Type', 'application/json');

        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $updatedResponseData = json_decode($response->getContent(), true);
        static::assertArraySubset($updateRequest, $updatedResponseData);
    }
}

<?php

namespace App\Tests\Api\Media\Video;

use App\Tests\Api\Media\AbstractMediaTest;

class CreateControllerTest extends AbstractMediaTest
{
    public function testCreate(): void
    {
        $client = static::createClient();
        $userData = $this->getUserDummyData();
        $this->createUserDummy();

        $requestData = [
            'description' => 'Video Description',
            'mood' => 'happy',
            'locations' => [
                'Luxembourg',
                'Italy',
            ],
            'duration' => 9,
            'recordedAt' => (new \DateTime())->format(\DateTimeInterface::ATOM),
        ];

        $client->jsonRequest('POST', '/videos', $requestData);
        static::assertResponseStatusCodeSame('401');

        $this->authenticateClient($client, $userData['email'], $userData['password']);
        $client->jsonRequest('POST', '/videos', $requestData);
        static::assertResponseStatusCodeSame('201');
        static::assertResponseHeaderSame('Content-Type', 'application/json');

        $response = $client->getResponse();

        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);

        self::assertArraySubset($requestData, $responseData);
    }
}

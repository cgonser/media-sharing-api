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

    public function testCreateWithMoments(): void
    {
        $client = static::createClient();
        $userData = $this->getUserDummyData();
        $this->createUserDummy();
        $this->authenticateClient($client, $userData['email'], $userData['password']);

        $momentRequestData = $this->getMomentDummyData();
        $client->jsonRequest('POST', '/moments', $momentRequestData);
        static::assertResponseStatusCodeSame('201');
        static::assertResponseHeaderSame('Content-Type', 'application/json');
        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $momentResponseData = json_decode($response->getContent(), true);

        $videoRequestData = $this->getVideoDummyData();
        $videoRequestData['moments'] = [
            [
                'position' => 1,
                'momentId' => $momentResponseData['id'],
            ]
        ];

        $client->jsonRequest('POST', '/videos', $videoRequestData);
        static::assertResponseStatusCodeSame('201');
        static::assertResponseHeaderSame('Content-Type', 'application/json');
        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $videoResponseData = json_decode($response->getContent(), true);
        $videoRequestData['moments'][0]['moment'] = $momentResponseData;

        self::assertArraySubset($videoRequestData, $videoResponseData);
    }
}

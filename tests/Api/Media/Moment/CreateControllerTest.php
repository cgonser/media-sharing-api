<?php

namespace App\Tests\Api\Media\Moment;

use App\Tests\Api\Media\AbstractMediaTest;

class CreateControllerTest extends AbstractMediaTest
{
    public function testCreate(): void
    {
        $client = static::createClient();
        $userData = $this->getUserDummyData();
        $this->createUserDummy();

        $requestData = [
            'mood' => 'happy',
            'location' => 'Luxembourg',
            'duration' => 3,
            'recordedAt' => (new \DateTime())->format(\DateTimeInterface::ATOM),
        ];

        $client->jsonRequest('POST', '/moments', $requestData);
        static::assertResponseStatusCodeSame('401');

        $this->authenticateClient($client, $userData['email'], $userData['password']);
        $client->jsonRequest('POST', '/moments', $requestData);
        static::assertResponseStatusCodeSame('201');
        static::assertResponseHeaderSame('Content-Type', 'application/json');

        $response = $client->getResponse();

        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);

        self::assertArraySubset($requestData, $responseData);
    }
}

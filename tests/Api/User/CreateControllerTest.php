<?php

namespace App\Tests\Api\User;

class CreateControllerTest extends AbstractUserTest
{
    public function testCreate(): void
    {
        $requestData = $this->getUserDummyData();

        $client = static::createClient();
        $client->jsonRequest('POST', '/users', $requestData);

        $response = $client->getResponse();

        static::assertResponseStatusCodeSame('201');
        static::assertResponseHeaderSame('Content-Type', 'application/json');

        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);
        $this->assertSame($requestData['name'], $responseData['name']);
        $this->assertSame($requestData['email'], $responseData['email']);
        $this->assertTrue($responseData['isActive']);
    }
}
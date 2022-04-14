<?php

namespace App\Tests\Api\User;

class ReadControllerTest extends AbstractUserTest
{
    public function testGet(): void
    {
        $client = static::createClient();

        $requestData = $this->getUserDummyData();

        $client->jsonRequest('POST', '/users', $requestData);
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);
        $userId = $responseData['id'];

        $client->jsonRequest('GET', '/users/'.$userId);
        static::assertResponseStatusCodeSame('401');

        $this->authenticateClient($client, $requestData['email'], $requestData['password']);

        $client->jsonRequest('GET', '/users/'.$userId);
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        static::assertResponseStatusCodeSame('200');
        static::assertResponseHeaderSame('Content-Type', 'application/json');

        $this->assertJson($response->getContent());
        $this->assertSame($requestData['name'], $responseData['name']);
        $this->assertSame($requestData['email'], $responseData['email']);
        $this->assertTrue($responseData['isActive']);
    }

    public function testGetCurrent(): void
    {
        $client = static::createClient();

        $requestData = $this->getUserDummyData();
        $this->createUserDummy();
        $this->authenticateClient($client, $requestData['email'], $requestData['password']);

        $client->jsonRequest('GET', '/users/current');

        static::assertResponseStatusCodeSame('200');
        static::assertResponseHeaderSame('Content-Type', 'application/json');

        $response = $client->getResponse();
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);
        $this->assertSame($requestData['name'], $responseData['name']);
        $this->assertSame($requestData['email'], $responseData['email']);
        $this->assertTrue($responseData['isActive']);
    }

    public function testGetAnother(): void
    {
        $client = static::createClient();

        $requestData = $this->getUserDummyData();
        $this->createUserDummy();

        $secondUserData = $requestData;
        $secondUserData['email'] = 'another-test@itinair.com';
        $secondUser = $this->createUserDummy($secondUserData);

        $this->authenticateClient($client, $requestData['email'], $requestData['password']);

        $client->jsonRequest('GET', '/users/'.$secondUser->getId()->toString());

        static::assertResponseStatusCodeSame('403');
    }
}

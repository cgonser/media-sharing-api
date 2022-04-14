<?php

namespace App\Tests\Api\User;

class UpdateControllerTest extends AbstractUserTest
{
    public function testUpdate(): void
    {
        $client = static::createClient();

        $requestData = $this->getUserDummyData();
        $user = $this->createUserDummy();

        $this->authenticateClient($client, $requestData['email'], $requestData['password']);

        $requestData['name'] = 'John Doe';

        $client->jsonRequest('PATCH', '/users/'.$user->getId()->toString(), $requestData);
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        static::assertResponseStatusCodeSame('200');
        static::assertResponseHeaderSame('Content-Type', 'application/json');

        $this->assertJson($response->getContent());
        $this->assertSame($user->getId()->toString(), $responseData['id']);
        $this->assertSame($requestData['name'], $responseData['name']);
        $this->assertSame($requestData['email'], $responseData['email']);
        $this->assertTrue($responseData['isActive']);
    }
}

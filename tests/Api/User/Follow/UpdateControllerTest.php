<?php

namespace App\Tests\Api\User\Follow;

use App\Tests\Api\User\AbstractUserTest;

class UpdateControllerTest extends AbstractUserTest
{
    public function testFollowApprove(): void
    {
        $client = static::createClient();

        $user1Data = $this->getUserDummyData();
        $user1Data['email'] = 'test-user-1@itinair.com';
        $this->createUserDummy($user1Data);

        $user2Data = $this->getUserDummyData();
        $user2Data['email'] = 'test-user-2@itinair.com';
        $this->createUserDummy($user2Data);

        $user3Data = $this->getUserDummyData();
        $user3Data['email'] = 'test-user-3@itinair.com';
        $user3Data['isProfilePrivate'] = true;
        $user3 = $this->createUserDummy($user3Data);

        $this->authenticateClient($client, $user1Data['email'], $user1Data['password']);
        $client->jsonRequest('POST', '/users/current/follows/'.$user3->getId()->toString());
        static::assertResponseStatusCodeSame('201');

        $this->authenticateClient($client, $user2Data['email'], $user2Data['password']);
        $client->jsonRequest('POST', '/users/current/follows/'.$user3->getId()->toString());
        static::assertResponseStatusCodeSame('201');

        $this->authenticateClient($client, $user3Data['email'], $user3Data['password']);

        $client->jsonRequest('GET', '/users/current/follows?isPending=1');
        static::assertResponseStatusCodeSame('200');
        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);

        $pendingApprovals = count($responseData);

        foreach ($responseData as $userFollow) {
            $client->jsonRequest('PUT', '/users/current/follows/'.$userFollow['id'].'/approval');
            static::assertResponseStatusCodeSame('204');
        }

        $client->jsonRequest('GET', '/users/current/follows?isPending=0');
        static::assertResponseStatusCodeSame('200');
        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);

        static::assertSame(count($responseData), $pendingApprovals);
    }
    public function testFollowRefuse(): void
    {
        $client = static::createClient();

        $user1Data = $this->getUserDummyData();
        $user1Data['email'] = 'test-user-1@itinair.com';
        $this->createUserDummy($user1Data);

        $user2Data = $this->getUserDummyData();
        $user2Data['email'] = 'test-user-2@itinair.com';
        $this->createUserDummy($user2Data);

        $user3Data = $this->getUserDummyData();
        $user3Data['email'] = 'test-user-3@itinair.com';
        $user3Data['isProfilePrivate'] = true;
        $user3 = $this->createUserDummy($user3Data);

        $this->authenticateClient($client, $user1Data['email'], $user1Data['password']);
        $client->jsonRequest('POST', '/users/current/follows/'.$user3->getId()->toString());
        static::assertResponseStatusCodeSame('201');

        $this->authenticateClient($client, $user2Data['email'], $user2Data['password']);
        $client->jsonRequest('POST', '/users/current/follows/'.$user3->getId()->toString());
        static::assertResponseStatusCodeSame('201');

        $this->authenticateClient($client, $user3Data['email'], $user3Data['password']);

        $client->jsonRequest('GET', '/users/current/follows?isPending=1');
        static::assertResponseStatusCodeSame('200');
        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);

        foreach ($responseData as $userFollow) {
            $client->jsonRequest('PUT', '/users/current/follows/'.$userFollow['id'].'/refusal');
            static::assertResponseStatusCodeSame('204');
        }

        $client->jsonRequest('GET', '/users/current/follows?isPending=1');
        static::assertResponseStatusCodeSame('200');
        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);

        static::assertSame(count($responseData), 0);

        $client->jsonRequest('GET', '/users/current/follows?isApproved=1');
        static::assertResponseStatusCodeSame('200');
        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);

        static::assertSame(count($responseData), 0);
    }
}

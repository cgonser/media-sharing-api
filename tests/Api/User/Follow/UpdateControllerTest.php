<?php

namespace App\Tests\Api\User\Follow;

use App\Tests\Api\User\AbstractUserTest;

class UpdateControllerTest extends AbstractUserTest
{
    public function testFollowApprove(): void
    {
        $client = static::createClient();

        $follower1Data = $this->getUserDummyData();
        $follower1Data['email'] = 'test-user-1@itinair.com';
        $this->createUserDummy($follower1Data);

        $follower2Data = $this->getUserDummyData();
        $follower2Data['email'] = 'test-user-2@itinair.com';
        $this->createUserDummy($follower2Data);

        $userData = $this->getUserDummyData();
        $userData['email'] = 'test-user-3@itinair.com';
        $userData['isProfilePrivate'] = true;
        $user = $this->createUserDummy($userData);

        $this->authenticateClient($client, $follower1Data['email'], $follower1Data['password']);
        $client->jsonRequest('POST', '/users/current/follows/'.$user->getId()->toString());
        static::assertResponseStatusCodeSame('201');

        $this->authenticateClient($client, $follower2Data['email'], $follower2Data['password']);
        $client->jsonRequest('POST', '/users/current/follows/'.$user->getId()->toString());
        static::assertResponseStatusCodeSame('201');

        $this->authenticateClient($client, $userData['email'], $userData['password']);

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

        $follower1Data = $this->getUserDummyData();
        $follower1Data['email'] = 'test-user-1@itinair.com';
        $this->createUserDummy($follower1Data);

        $follower2Data = $this->getUserDummyData();
        $follower2Data['email'] = 'test-user-2@itinair.com';
        $this->createUserDummy($follower2Data);

        $userData = $this->getUserDummyData();
        $userData['email'] = 'test-user-3@itinair.com';
        $userData['isProfilePrivate'] = true;
        $user = $this->createUserDummy($userData);

        $this->authenticateClient($client, $follower1Data['email'], $follower1Data['password']);
        $client->jsonRequest('POST', '/users/current/follows/'.$user->getId()->toString());
        static::assertResponseStatusCodeSame('201');

        $this->authenticateClient($client, $follower2Data['email'], $follower2Data['password']);
        $client->jsonRequest('POST', '/users/current/follows/'.$user->getId()->toString());
        static::assertResponseStatusCodeSame('201');

        $this->authenticateClient($client, $userData['email'], $userData['password']);

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

<?php

namespace App\Tests\Api\User\Follow;

use App\Tests\Api\User\AbstractUserTest;

class DeleteControllerTest extends AbstractUserTest
{
    public function testUnfollow(): void
    {
        $client = static::createClient();

        $followerData = $this->getUserDummyData();
        $followerData['email'] = 'test-user-1@itinair.com';
        $this->createUserDummy($followerData);

        $userData = $this->getUserDummyData();
        $userData['email'] = 'test-user-2@itinair.com';
        $user = $this->createUserDummy($userData);

        $this->authenticateClient($client, $followerData['email'], $followerData['password']);
        $client->jsonRequest('POST', '/users/current/follows/'.$user->getId()->toString());
        static::assertResponseStatusCodeSame('201');

        $client->jsonRequest('GET', '/users/current/follows');
        static::assertResponseStatusCodeSame('200');
        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);

        foreach ($responseData as $userFollow) {
            $client->jsonRequest('DELETE', '/users/current/follows/'.$userFollow['followingId']);
            static::assertResponseStatusCodeSame('204');
        }

        $client->jsonRequest('GET', '/users/current/follows');
        static::assertResponseStatusCodeSame('200');
        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);

        static::assertSame(count($responseData), 0);
    }
}

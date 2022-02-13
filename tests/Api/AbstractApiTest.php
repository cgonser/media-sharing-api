<?php

namespace App\Tests\Api;

use App\User\Entity\User;
use App\User\Service\UserManager;
use App\User\Service\UserPasswordManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractApiTest extends WebTestCase
{
    protected function createAuthenticatedClient(string $email, string $password): KernelBrowser
    {
        $client = static::createClient();

        $this->authenticateClient($client, $email, $password);

        return $client;
    }

    protected function authenticateClient(KernelBrowser $client, string $email, string $password): void
    {
        $client->jsonRequest(
            'POST',
            '/users/login',
            [
                'username' => $email,
                'password' => $password,
            ]
        );

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $responseData['token']));
    }

    protected function getUserDummyData(): array
    {
        return [
            'name' => 'Test User',
            'email' => 'test-user@itinair.com',
            'password' => '123',
        ];
    }

    protected function createUserDummy(?array $userData = null): User
    {
        if (null === $userData) {
            $userData = $this->getUserDummyData();
        }

        $user = new User();
        $user->setName($userData['name']);
        $user->setEmail($userData['email']);
        $user->setPassword(
            static::getContainer()->get(UserPasswordManager::class)->encodePassword($user, $userData['password'])
        );

        static::getContainer()->get(UserManager::class)->create($user);

        return $user;
    }

    protected function promoteUser(User $user): void
    {
        static::getContainer()->get(UserManager::class)->promote($user);
    }
}
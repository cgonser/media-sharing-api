<?php

namespace App\Tests\Integration\User;

use App\User\Exception\UserInvalidPasswordException;
use App\User\Request\UserRequest;
use App\User\Service\UserPasswordManager;
use App\User\Service\UserRequestManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserPasswordManagerTest extends KernelTestCase
{
    private function getService(): UserPasswordManager
    {
        return static::getContainer()->get(UserPasswordManager::class);
    }

    public function testChangePassword(): void
    {
        $userRequest = new UserRequest();
        $userRequest->name = 'Test User';
        $userRequest->email = 'test-user@itinair.com';
        $userRequest->password = '123';

        $user = static::getContainer()->get(UserRequestManager::class)->createFromRequest($userRequest);

        $newPassword = '1234';
        $this->getService()->changePassword($user, $userRequest->password, $newPassword);
        $this->assertTrue($this->getService()->validatePassword($user, $newPassword));

        $this->expectException(UserInvalidPasswordException::class);
        $this->getService()->changePassword($user, $userRequest->password, $newPassword);
    }
}

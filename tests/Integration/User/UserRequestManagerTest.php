<?php

namespace App\Tests\Integration\User;

use App\Core\Exception\InvalidEntityException;
use App\User\Entity\User;
use App\User\Exception\UserInvalidRoleException;
use App\User\Exception\UserRoleNotFoundException;
use App\User\Request\UserRequest;
use App\User\Service\UserRequestManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRequestManagerTest extends KernelTestCase
{
    private function getService(): UserRequestManager
    {
        return static::getContainer()->get(UserRequestManager::class);
    }

    public function testRequestMapping(): void
    {
        $userRequest = new UserRequest();
        $userRequest->name = 'Test User';
        $userRequest->username = 'test-user';
        $userRequest->email = 'test-user@itinair.com';
        $userRequest->password = '123';

        $user = new User();
        $this->getService()->mapFromRequest($user, $userRequest);

        $this->assertEquals($userRequest->name, $user->getName());
        $this->assertEquals($userRequest->email, $user->getEmail());
        $this->assertNotNull($user->getPassword());
    }

    public function testCreateFromRequest(): void
    {
        $userRequest = new UserRequest();
        $userRequest->name = 'Test User';
        $userRequest->username = 'test-user';
        $userRequest->email = 'test-user@itinair.com';
        $userRequest->password = '123';

        $user = $this->getService()->createFromRequest($userRequest);

        $this->assertEquals($userRequest->name, $user->getName());
        $this->assertEquals($userRequest->email, $user->getEmail());
        $this->assertNotNull($user->getPassword());

        $this->expectException(InvalidEntityException::class);
        $this->getService()->createFromRequest($userRequest);
    }

    public function testUpdateFromRequest(): void
    {
        $userRequest = new UserRequest();
        $userRequest->name = 'Test User';
        $userRequest->email = 'test-user@itinair.com';
        $userRequest->username = 'test-user';
        $userRequest->password = '123';

        $user = $this->getService()->createFromRequest($userRequest);

        $userUpdateRequest = new UserRequest();
        $userUpdateRequest->name = 'Test User 2';
        $userUpdateRequest->email = 'test-user-2@itinair.com';
        $userUpdateRequest->username = 'test-user-2';
        $this->getService()->updateFromRequest($user, $userUpdateRequest);

        $this->assertEquals($userUpdateRequest->name, $user->getName());
        $this->assertEquals($userUpdateRequest->email, $user->getEmail());
    }

    public function testAddRole(): void
    {
        $user = new User();
        $user->setName('Test User');
        $user->setEmail('test-user@itinair.com');
        $user->setUsername('test-user');

        $this->getService()->addRole($user, User::ROLE_ADMIN);
        $this->assertEquals([User::ROLE_ADMIN], $user->getRoles());
        $this->assertTrue($user->hasRole(User::ROLE_ADMIN));

        $this->getService()->addRole($user, User::ROLE_ADMIN);
        $this->assertEquals([User::ROLE_ADMIN], $user->getRoles());
        $this->assertTrue($user->hasRole(User::ROLE_ADMIN));

        $this->getService()->addRole($user, User::ROLE_USER);
        $this->assertEqualsCanonicalizing($user->getRoles(), [User::ROLE_USER, User::ROLE_ADMIN]);
        $this->assertTrue($user->hasRole(User::ROLE_ADMIN));
        $this->assertTrue($user->hasRole(User::ROLE_USER));

        $this->expectException(UserInvalidRoleException::class);
        $this->getService()->addRole($user, 'non-existing-role');
    }

    public function testRemoveRole(): void
    {
        $user = new User();
        $user->setName('Test User');
        $user->setEmail('test-user@itinair.com');
        $user->setUsername('test-user');

        $this->getService()->addRole($user, User::ROLE_ADMIN);
        $this->assertEquals([User::ROLE_ADMIN], $user->getRoles());
        $this->assertTrue($user->hasRole(User::ROLE_ADMIN));

        $this->getService()->removeRole($user, User::ROLE_ADMIN);
        $this->assertEquals([], $user->getRoles());
        $this->assertFalse($user->hasRole(User::ROLE_ADMIN));

        $this->getService()->addRole($user, User::ROLE_USER);
        $this->assertEqualsCanonicalizing($user->getRoles(), [User::ROLE_USER]);
        $this->assertTrue($user->hasRole(User::ROLE_USER));

        $this->getService()->removeRole($user, User::ROLE_USER);
        $this->assertEquals([], $user->getRoles());
        $this->assertFalse($user->hasRole(User::ROLE_USER));

        $this->expectException(UserRoleNotFoundException::class);
        $this->getService()->removeRole($user, User::ROLE_USER);
    }
}

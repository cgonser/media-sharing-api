<?php

namespace App\User\Service;

use App\Core\Validation\EntityValidator;
use App\User\Entity\User;
use App\User\Exception\UserInvalidUsernameException;
use App\User\Message\UserCreatedEvent;
use App\User\Message\UserStatusChangedEvent;
use App\User\Message\UserUpdatedEvent;
use App\User\Repository\UserRepository;
use GeoIp2\Database\Reader;
use Symfony\Component\Intl\Timezones;
use Symfony\Component\Messenger\MessageBusInterface;

class UserManager
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityValidator $validator,
        private readonly MessageBusInterface $messageBus,
        private readonly Reader $geoIpReader,
        private readonly string $defaultLocale,
        private readonly string $defaultTimezone,
        private readonly string $userTestEmailMask,
    ) {
    }

    public function create(User $user): void
    {
        if (0 === count($user->getRoles())) {
            $user->addRole(User::ROLE_USER);
        }

        $this->save($user);

        $this->configureTestUser($user);

        $this->messageBus->dispatch(new UserCreatedEvent($user->getId()));
    }

    public function update(User $user): void
    {
        $this->save($user);

        $this->messageBus->dispatch(new UserUpdatedEvent($user->getId()));
    }

    public function save(User $user): void
    {
        $this->validateUser($user);
        $this->userRepository->save($user);
    }

    public function persist(User $user): void
    {
        $this->validateUser($user);
        $this->userRepository->persist($user);
    }

    public function flush(): void
    {
        $this->userRepository->flush();
    }

    private function validateUser(User $user)
    {
        $this->validator->validate($user);

        if ($this->isUsernameBlacklisted($user->getUsername())) {
            throw new UserInvalidUsernameException();
        }
    }

    private function isUsernameBlacklisted(string $username): bool
    {
        return false;
    }

    public function localizeUser(User $user, string $ipAddress): void
    {
        try {
            $countryRecord = $this->geoIpReader->country($ipAddress);

            if (null === $user->getCountry()) {
                $user->setCountry($countryRecord->country->isoCode);
            }

            if (null === $user->getLocale()) {
                $user->setLocale($this->defaultLocale);
            }

            if (null === $user->getTimezone()) {
                $user->setTimezone(Timezones::forCountryCode($user->getCountry())[0]);
            }
        } catch (\Exception) {
            // do nothing
        }
    }

    public function applyDefaultLocalization(User $user): void
    {
        $user
            ->setLocale($this->defaultLocale)
            ->setTimezone($this->defaultTimezone)
        ;
    }

    public function blockUser(User $user, ?string $notes = null): void
    {
        $user->setIsBlocked(true);

        if (null !== $notes) {
            $user->appendAdminNotes($notes);
        }

        $this->userRepository->save($user);

        $this->messageBus->dispatch(
            new UserStatusChangedEvent($user->getId(), $user->isActive(), $user->isBlocked())
        );
    }

    public function unblockUser(User $user, ?string $notes = null): void
    {
        $user->setIsBlocked(false);

        if (null !== $notes) {
            $user->appendAdminNotes($notes);
        }

        $this->userRepository->save($user);

        $this->messageBus->dispatch(
            new UserStatusChangedEvent($user->getId(), $user->isActive(), $user->isBlocked())
        );
    }

    private function configureTestUser(User $user): void
    {
        if (0 === preg_match($this->userTestEmailMask, $user->getEmail())) {
            return;
        }

        $user
            ->setIsTestUser(true)
            ->setIsEmailValidated(true)
            ->setEmailValidatedAt(new \DateTime())
        ;

        $this->save($user);
    }
}

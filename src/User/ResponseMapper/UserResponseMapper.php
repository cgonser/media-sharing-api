<?php

namespace App\User\ResponseMapper;

use App\User\Dto\PublicUserDto;
use App\User\Dto\UserDto;
use App\User\Entity\User;
use Aws\S3\S3Client;

class UserResponseMapper
{
    public function __construct(
        private S3Client $s3Client,
        private string $userImageS3Bucket
    ) {
    }

    public function map(User $user): UserDto
    {
        $userDto = new UserDto();
        $userDto->id = $user->getId();
        $userDto->name = $user->getName();
        $userDto->displayName = $user->getDisplayName();
        $userDto->bio = $user->getBio();
        $userDto->email = $user->getEmail();
        $userDto->timezone = $user->getTimezone();
        $userDto->locale = $user->getLocale();
        $userDto->phoneNumber = $user->getPhoneNumber();
        $userDto->country = $user->getCountry();
        $userDto->allowEmailMarketing = $user->allowEmailMarketing();
        $userDto->isProfilePrivate = $user->isProfilePrivate();
        $userDto->isActive = $user->isActive();
        $userDto->lastLoginAt = $user->getLastLoginAt()?->format(\DateTimeInterface::ATOM);
        $userDto->emailValidatedAt = $user->getEmailValidatedAt()?->format(\DateTimeInterface::ATOM);

        if (null !== $user->getProfilePicture()) {
            $userDto->profilePicture = $this->getProfilePictureUrl($user);
        }

        return $userDto;
    }

    public function mapPublic(User $user): PublicUserDto
    {
        $userDto = new PublicUserDto();
        $userDto->id = $user->getId();
        $userDto->displayName = $user->getDisplayName();
        $userDto->bio = $user->getBio();
        $userDto->isProfilePrivate = $user->isProfilePrivate();

        if (null !== $user->getProfilePicture()) {
            $userDto->profilePicture = $this->getProfilePictureUrl($user);
        }

        return $userDto;
    }

    public function getProfilePictureUrl(User $user): ?string
    {
        if (null === $user->getProfilePicture() || '' === trim($user->getProfilePicture())) {
            return null;
        }

        return $this->s3Client->getObjectUrl(
            $this->userImageS3Bucket,
            $user->getProfilePicture()
        );
    }

    public function mapMultiple(array $users): array
    {
        $userDtos = [];

        foreach ($users as $user) {
            $userDtos[] = $this->map($user);
        }

        return $userDtos;
    }

    public function mapMultiplePublic(array $users): array
    {
        $userDtos = [];

        foreach ($users as $user) {
            $userDtos[] = $this->mapPublic($user);
        }

        return $userDtos;
    }
}

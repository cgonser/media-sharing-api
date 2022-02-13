<?php

namespace App\User\ResponseMapper;

use App\Localization\Provider\CurrencyProvider;
use App\User\Dto\UserDto;
use App\User\Entity\User;
use Aws\S3\S3Client;

class UserResponseMapper
{
    public function __construct(
        private CurrencyProvider $currencyProvider,
        private S3Client $s3Client,
        private string $userImageS3Bucket
    ) {
    }

    public function map(User $user): UserDto
    {
        $userDto = new UserDto();
        $userDto->id = $user->getId();
        $userDto->name = $user->getName();
        $userDto->email = $user->getEmail();
        $userDto->timezone = $user->getTimezone();
        $userDto->locale = $user->getLocale();
        $userDto->country = $user->getCountry();
        $userDto->allowEmailMarketing = $user->allowEmailMarketing();
        $userDto->isActive = $user->isActive();
        $userDto->lastLoginAt = $user->getLastLoginAt()?->format(\DateTimeInterface::ATOM);
        $userDto->emailValidatedAt = $user->getEmailValidatedAt()?->format(\DateTimeInterface::ATOM);

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
}

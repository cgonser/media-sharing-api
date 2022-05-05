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
        $this->mapBaseData($userDto, $user);

        $userDto->name = $user->getName();
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

        return $userDto;
    }

    public function mapPublic(User $user, ?bool $isFollowed = null): PublicUserDto
    {
        $userDto = new PublicUserDto();

        $this->mapBaseData($userDto, $user);

        if (null !== $isFollowed) {
            $userDto->isFollowed = $isFollowed;
        }

        return $userDto;
    }

    private function mapBaseData(UserDto|PublicUserDto $userDto, User $user): void
    {
        $userDto->id = $user->getId();
        $userDto->username = $user->getUsername();
        $userDto->displayName = $user->getDisplayName();
        $userDto->bio = $user->getBio();
        $userDto->isProfilePrivate = $user->isProfilePrivate();
        $userDto->followersCount = $user->getFollowersCount();
        $userDto->followingCount = $user->getFollowingCount();
        $userDto->videoCount = $user->getVideoCount();

        if (null !== $user->getProfilePicture()) {
            $userDto->profilePicture = $this->getProfilePictureUrl($user);
        }
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

    public function mapMultiple(array $users, ?array $followingIds = null): array
    {
        $userDtos = [];

        foreach ($users as $user) {
            $userDtos[] = $this->mapPublic($user);
        }

        if (null !== $followingIds) {
            $this->appendMultipleFollowingDetails($userDtos, $followingIds);
        }

        return $userDtos;
    }

    public function appendFollowingFlag(PublicUserDto $userDto, array $followingIds): void
    {
        $userDto->isFollowed = count(array_filter($followingIds, fn($followingId) => $userDto->id === $followingId));
    }

    public function appendMultipleFollowingDetails(array $userDtos, array $followingIds): array
    {
        foreach ($userDtos as $userDto) {
            $this->appendFollowingFlag($userDto, $followingIds);
        }

        return $userDtos;
    }
}

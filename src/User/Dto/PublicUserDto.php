<?php

namespace App\User\Dto;

class PublicUserDto
{
    public string $id;

    public ?string $username;

    public ?string $displayName;

    public ?string $bio;

    public ?string $profilePicture;

    public int $followersCount;

    public int $followingCount;

    public ?bool $isFollowed = null;

    public bool $isProfilePrivate;
}

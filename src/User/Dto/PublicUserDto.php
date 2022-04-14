<?php

namespace App\User\Dto;

class PublicUserDto
{
    public string $id;

    public ?string $displayName;

    public ?string $bio;

    public ?string $profilePicture;

    public bool $isProfilePrivate;
}

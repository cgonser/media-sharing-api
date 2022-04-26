<?php

namespace App\User\Dto;

class UserDto
{
    public string $id;

    public ?string $name;

    public ?string $username;

    public ?string $displayName;

    public ?string $bio;

    public ?string $email;

    public ?string $profilePicture;

    public ?string $phoneNumber;

    public ?string $country;

    public ?string $currencyId;

    public ?string $currencyCode;

    public ?string $locale;

    public ?string $timezone;

    public ?bool $allowEmailMarketing;

    public ?string $emailValidatedAt;

    public ?string $lastLoginAt;

    public bool $isProfilePrivate;

    public bool $isActive;
}

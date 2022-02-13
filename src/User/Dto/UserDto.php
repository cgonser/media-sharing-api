<?php

namespace App\User\Dto;

class UserDto
{
    public string $id;

    public ?string $name;

    public ?string $email;

    public ?string $phone;

    public ?string $profilePicture;

    public ?string $jobTitle;

    public ?string $country;

    public ?string $currencyId;

    public ?string $currencyCode;

    public ?string $locale;

    public ?string $timezone;

    public ?bool $allowEmailMarketing;

    public ?string $emailValidatedAt;

    public ?string $lastLoginAt;

    public bool $isActive;
}

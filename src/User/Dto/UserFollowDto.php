<?php

namespace App\User\Dto;

class UserFollowDto
{
    public string $id;

    public string $followerId;

    public ?PublicUserDto $follower;

    public string $followingId;

    public ?PublicUserDto $following;

    public ?bool $isApproved = null;

    public string $createdAt;

    public string $updatedAt;
}

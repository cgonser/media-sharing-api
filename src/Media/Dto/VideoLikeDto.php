<?php

namespace App\Media\Dto;

use App\User\Dto\PublicUserDto;

class VideoLikeDto
{
    public ?string $id;

    public ?string $userId;

    public ?PublicUserDto $user;

    public ?string $createdAt;
}

<?php

namespace App\Media\Dto;

use App\User\Dto\PublicUserDto;

class VideoCommentDto
{
    public ?string $id;

    public ?string $userId;

    public ?PublicUserDto $user;

    public ?string $comment;

    public ?string $createdAt;
}

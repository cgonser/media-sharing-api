<?php

namespace App\Media\Dto;

class MediaItemDto
{
    public string $id;

    public ?string $uploadUrl;

    public ?string $uploadUrlValidUntil;

    public ?string $publicUrl;

    public ?string $status;

    public ?string $type;

    public ?string $comments;

    public ?string $createdAt;
}

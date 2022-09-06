<?php

namespace App\Media\Dto;

class MusicDto
{
    public string $id;

    public string $displayName;

    public ?string $artist;

    public ?string $title;

    public string $publicUrl;

    public ?float $duration;
}

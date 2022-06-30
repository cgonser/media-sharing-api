<?php

namespace App\Media\Dto;

class VideoMomentDto
{
    public ?string $momentId;

    public ?MomentDto $moment;

    public ?int $position;

    public ?float $duration;
}

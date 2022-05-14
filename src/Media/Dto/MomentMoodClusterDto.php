<?php

namespace App\Media\Dto;

class MomentMoodClusterDto
{
    public string $mood;

    /** @var array MomentMoodDto[] */
    public array $moments = [];
}
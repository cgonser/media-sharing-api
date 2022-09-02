<?php

namespace App\Media\Dto;

class MediaConverterInsertableImageDto
{
    public int $width;
    public int $height;
    public int $x;
    public int $y;
    public int $layer;
    public string $input;
    public string $startTime;
    public int $opacity;
}
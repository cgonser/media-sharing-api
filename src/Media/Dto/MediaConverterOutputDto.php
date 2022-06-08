<?php

namespace App\Media\Dto;

use App\Media\Enumeration\MediaItemExtension;
use App\Media\Enumeration\MediaItemType;

class MediaConverterOutputDto
{
    public MediaItemType $mediaItemType;

    public MediaItemExtension $mediaItemExtension;

    public ?int $width = null;

    public ?int $height = null;

    public ?int $maxBitrate = null;

    public ?string $nameModifier;

    public ?string $filename = null;
}
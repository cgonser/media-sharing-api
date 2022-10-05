<?php

namespace App\Media\Enumeration;

enum VideoStatus: string
{
    case PENDING = 'pending';
    case PUBLISHED = 'published';
    case HIDDEN = 'hidden';
    case PREVIEW = 'preview';

    public const GENERATED_STATUSES = [
        self::PUBLISHED,
        self::HIDDEN,
        self::PREVIEW,
    ];

    public static function isGenerated(self $videoStatus): bool
    {
        return in_array($videoStatus, self::GENERATED_STATUSES);
    }
}

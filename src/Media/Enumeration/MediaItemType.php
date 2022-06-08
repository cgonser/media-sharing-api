<?php

namespace App\Media\Enumeration;

enum MediaItemType: string
{
    case VIDEO_ORIGINAL = 'video_original';
    case VIDEO_HIGH = 'video_high';
    case VIDEO_MEDIUM = 'video_medium';
    case VIDEO_LOW = 'video_low';

    case IMAGE_THUMBNAIL = 'image_thumbnail';

    // deprecated
    case VIDEO = 'video';
    // deprecated
    case THUMBNAIL = 'thumbnail';

    public const VIDEO_TYPES = [
        self::VIDEO_ORIGINAL,
        self::VIDEO_HIGH,
        self::VIDEO_MEDIUM,
        self::VIDEO_LOW,
    ];

    public const IMAGE_TYPES = [
        self::IMAGE_THUMBNAIL,
    ];

    public static function isVideo(self $mediaItemType): bool
    {
        return in_array($mediaItemType, self::VIDEO_TYPES);
    }

    public static function isImage(self $mediaItemType): bool
    {
        return in_array($mediaItemType, self::IMAGE_TYPES);
    }
}

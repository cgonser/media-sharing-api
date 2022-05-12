<?php

namespace App\Media\Enumeration;

enum MediaItemStatus: string
{
    case UPLOAD_PENDING = 'upload_pending';
    case AVAILABLE = 'available';
}

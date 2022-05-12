<?php

namespace App\Media\Enumeration;

enum MomentStatus: string
{
    case PENDING = 'pending';
    case PUBLISHED = 'published';
    case HIDDEN = 'hidden';
}
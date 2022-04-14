<?php

namespace App\Media\Security;

use App\Media\Entity\Video;
use App\User\Security\AbstractUserAuthorizationVoter;

class VideoVoter extends AbstractUserAuthorizationVoter
{
    public function isSubjectSupported($subject): bool
    {
        return $subject instanceof Video || $subject === Video::class;
    }
}

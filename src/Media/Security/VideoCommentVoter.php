<?php

namespace App\Media\Security;

use App\Media\Entity\VideoComment;
use App\User\Security\AbstractUserAuthorizationVoter;

class VideoCommentVoter extends AbstractUserAuthorizationVoter
{
    public function isSubjectSupported($subject): bool
    {
        return $subject instanceof VideoComment || $subject === VideoComment::class;
    }
}

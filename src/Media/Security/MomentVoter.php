<?php

namespace App\Media\Security;

use App\Media\Entity\Moment;
use App\User\Security\AbstractUserAuthorizationVoter;

class MomentVoter extends AbstractUserAuthorizationVoter
{
    public function isSubjectSupported($subject): bool
    {
        return $subject instanceof Moment || $subject === Moment::class;
    }
}

<?php

namespace App\User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;

#[ORM\Entity]
#[ORM\Table('refresh_tokens')]
class UserRefreshToken extends BaseRefreshToken
{
}

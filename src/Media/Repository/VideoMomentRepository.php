<?php

namespace App\Media\Repository;

use App\Core\Repository\BaseRepository;
use App\Media\Entity\VideoMoment;
use Doctrine\Persistence\ManagerRegistry;

class VideoMomentRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VideoMoment::class);
    }
}

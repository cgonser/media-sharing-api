<?php

namespace App\Media\Repository;

use App\Core\Repository\BaseRepository;
use App\Media\Entity\VideoLocation;
use Doctrine\Persistence\ManagerRegistry;

class VideoLocationRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VideoLocation::class);
    }
}

<?php

namespace App\Media\Repository;

use App\Core\Repository\BaseRepository;
use App\Media\Entity\VideoLike;
use Doctrine\Persistence\ManagerRegistry;

class VideoLikeRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VideoLike::class);
    }
}

<?php

namespace App\Media\Repository;

use App\Core\Repository\BaseRepository;
use App\Media\Entity\VideoComment;
use Doctrine\Persistence\ManagerRegistry;

class VideoCommentRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VideoComment::class);
    }
}

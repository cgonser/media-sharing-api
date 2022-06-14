<?php

namespace App\Media\Repository;

use App\Core\Repository\BaseRepository;
use App\Media\Entity\Music;
use Doctrine\Persistence\ManagerRegistry;

class MusicRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Music::class);
    }
}

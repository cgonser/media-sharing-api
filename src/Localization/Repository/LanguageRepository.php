<?php

namespace App\Localization\Repository;

use App\Core\Repository\BaseRepository;
use App\Localization\Entity\Language;
use Doctrine\Persistence\ManagerRegistry;

class LanguageRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Language::class);
    }
}

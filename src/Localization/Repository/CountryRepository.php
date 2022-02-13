<?php

namespace App\Localization\Repository;

use App\Core\Repository\BaseRepository;
use App\Localization\Entity\Country;
use Doctrine\Persistence\ManagerRegistry;

class CountryRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }
}

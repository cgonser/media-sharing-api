<?php

namespace App\Localization\Repository;

use App\Core\Repository\BaseRepository;
use App\Localization\Entity\Currency;
use Doctrine\Persistence\ManagerRegistry;

class CurrencyRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Currency::class);
    }
}

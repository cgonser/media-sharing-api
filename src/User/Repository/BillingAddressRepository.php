<?php

namespace App\User\Repository;

use App\Core\Repository\BaseRepository;
use App\User\Entity\BillingAddress;
use Doctrine\Persistence\ManagerRegistry;

class BillingAddressRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BillingAddress::class);
    }
}

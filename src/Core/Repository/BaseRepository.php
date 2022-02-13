<?php

namespace App\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class BaseRepository extends ServiceEntityRepository
{
    public function save($object)
    {
        $this->getEntityManager()->persist($object);
        $this->getEntityManager()->flush();
    }

    public function delete($object)
    {
        $this->getEntityManager()->remove($object);
        $this->getEntityManager()->flush();
    }

    public function persist($object)
    {
        $this->getEntityManager()->persist($object);
    }

    public function flush()
    {
        $this->getEntityManager()->flush();
    }
}

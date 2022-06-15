<?php

namespace App\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class BaseRepository extends ServiceEntityRepository
{
    public function save($object): void
    {
        $this->getEntityManager()->persist($object);
        $this->getEntityManager()->flush();
    }

    public function delete($object): void
    {
        $this->getEntityManager()->remove($object);
        $this->getEntityManager()->flush();
    }

    public function refresh($object): void
    {
        $this->getEntityManager()->refresh($object);
    }

    public function persist($object): void
    {
        $this->getEntityManager()->persist($object);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}

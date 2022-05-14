<?php

namespace App\Media\Repository;

use App\Core\Repository\BaseRepository;
use App\Media\Entity\Moment;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

class MomentRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Moment::class);
    }

    public function findByAreaGroupedByMood(float $longMin, float $longMax, float $latMin, float $latMax): array
    {
        $sqlQuery = <<<QUERY
            SELECT  l.long,
                    l.lat,
                    m.mood,
                    COUNT(*) AS moments,
                    ST_ClusterDBSCAN(l.coordinates, eps := :distance, minpoints := 2) OVER w AS cluster_id
            FROM    location l
            JOIN    moment m ON ( m.location_id = l.id )
            WHERE   l.long BETWEEN :long_min AND :long_max
            AND     l.lat BETWEEN :lat_min AND :lat_max
            GROUP BY l.long, l.lat, l.coordinates, m.mood
            WINDOW w AS (PARTITION BY m.mood ORDER BY m.mood)
            ORDER BY mood, cluster_id
            QUERY;

        $distance = ($latMax - $latMin) / 10;

        return $this->getEntityManager()
            ->getConnection()
            ->prepare($sqlQuery)
            ->executeQuery(
                [
                    'distance' => $distance,
                    'long_min' => $longMin,
                    'long_max' => $longMax,
                    'lat_min' => $latMin,
                    'lat_max' => $latMax,
                ]
            )
            ->fetchAllAssociative();
    }
}

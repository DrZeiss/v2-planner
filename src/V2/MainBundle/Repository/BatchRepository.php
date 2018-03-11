<?php

namespace V2\MainBundle\Repository;

/**
 * BatchRepository
 *
 */
class BatchRepository extends \Doctrine\ORM\EntityRepository
{
    public function getNextAvailableBatchNumber()
    {
        $result = $this->createQueryBuilder('b')
            ->select("MAX(b.id) AS highest_batch_number")
            ->getQuery()
            ->getResult();

        return (int)$result[0]['highest_batch_number'] + 1;
    }

    public function findUnreceivedScheduledBatches($parameters)
    {
        $color                  = $parameters['color'];
        $vendor                 = $parameters['vendor'];
        $estimatedReleaseDate   = $parameters['estimated_release_date'];

        $qb = $this->createQueryBuilder('b')
            ->join('b.paint', 'paint')
            ->where('b.id > 0')
            ->andWhere('b.receivedDate IS NULL');

        if ($color) {
            $qb->andWhere("(b.color = :color")
                ->setParameter('color', $color);
        }

        if ($vendor) {
            $qb->andWhere("b.vendor LIKE :vendor")
                ->setParameter('vendor', "%" . $vendor . "%");
        }

        if ($estimatedReleaseDate) {
            $qb->andWhere("b.estimatedReleaseDate = :estimatedReleaseDate")
                ->setParameter('estimatedReleaseDate', new \DateTime($estimatedReleaseDate));
        }
        
        $results = $qb->addOrderBy("b.estimatedDeliveryDate", "ASC")
            ->addOrderBy("b.estimatedReleaseDate", "ASC")
            ->getQuery()
            ->getResult();

        return $results;
    }

}
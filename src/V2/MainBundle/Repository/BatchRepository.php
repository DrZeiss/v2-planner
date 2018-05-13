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
        $batch                  = $parameters['batch'];
        $showAllBatches         = $parameters['show_all_batches'];

        $qb = $this->createQueryBuilder('b')
            ->addSelect('CASE WHEN b.vendor IS NULL THEN 1 ELSE 2 END AS HIDDEN ordering1')
            ->join('b.paints', 'paint')
            ->where('b.id > 0');

        if ($color) {
            $qb->andWhere("b.color = :color")
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

        if ($batch) {
            $qb->andWhere("b.id = :batch")
                ->setParameter('batch', $batch);
        }

        if (!$showAllBatches) {
            $qb->andWhere('b.receivedDate IS NULL');
        }
        
        $results = $qb->addOrderBy("ordering1")
            ->addOrderBy("b.neededByDate", "ASC")
            ->addOrderBy("b.estimatedReleaseDate", "ASC")
            ->getQuery()
            ->getResult();

        return $results;
    }

}

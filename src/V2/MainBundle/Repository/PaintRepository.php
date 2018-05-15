<?php

namespace V2\MainBundle\Repository;

/**
 * PaintRepository
 *
 */
class PaintRepository extends \Doctrine\ORM\EntityRepository
{
    public function findPaint($parameters)
    {
        $plannerEstimatedShipDate   = $parameters['planner_esd'];

        $sql = "SELECT max(color) AS color, sum(num_jobs) AS num_jobs, sum(sum_qty) AS sum_qty, min(planner_esd) AS planner_esd 
        FROM (
            SELECT color_1 AS color, count(DISTINCT p.job_id) AS num_jobs, sum(job.quantity) AS sum_qty, min(planner_estimated_ship_date) AS planner_esd 
            FROM paint p
            JOIN job ON job.id = p.job_id
            JOIN kitting k ON k.job_id = p.job_id AND (k.kitting_short_1_id IS NOT NULL OR 
                                                       k.kitting_short_2_id IS NOT NULL OR 
                                                       k.kitting_short_3_id IS NOT NULL OR 
                                                       k.kitting_short_4_id IS NOT NULL)
            LEFT JOIN kitting_short ks on ks.kitting_id = k.id
            WHERE color_1 IS NOT NULL
            AND batch_1_id IS NULL
            AND ks.painted_part = 1
            AND job.cancelled_date IS NULL
            GROUP BY color_1
                UNION
            SELECT color_2 AS color, count(DISTINCT p.job_id), sum(job.quantity), min(planner_estimated_ship_date) AS planner_esd 
            FROM paint p
            JOIN job ON job.id = p.job_id
            JOIN kitting k ON k.job_id = p.job_id AND (k.kitting_short_1_id IS NOT NULL OR 
                                                       k.kitting_short_2_id IS NOT NULL OR 
                                                       k.kitting_short_3_id IS NOT NULL OR 
                                                       k.kitting_short_4_id IS NOT NULL)
            LEFT JOIN kitting_short ks on ks.kitting_id = k.id
            WHERE color_2 IS NOT NULL
            AND batch_2_id IS NULL
            AND ks.painted_part = 1
            AND job.cancelled_date IS NULL
            GROUP BY color_2
            ) tmp
        GROUP BY tmp.color";
        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findUnscheduledPaintJobs($parameters)
    {
        $name                       = $parameters['name'];
        $color                      = $parameters['color'];
        $plannerEstimatedShipDate   = $parameters['planner_esd'];

        $qb = $this->createQueryBuilder('p')
            ->join('p.job', 'job')
            ->join('job.kitting', 'kitting')
            ->leftJoin('kitting.kittingShort1', 'kittingShort1')
            ->leftJoin('kitting.kittingShort2', 'kittingShort2')
            ->leftJoin('kitting.kittingShort3', 'kittingShort3')
            ->leftJoin('kitting.kittingShort4', 'kittingShort4')
            ->where('p.id > 0')
            ->andWhere("(kitting.kittingShort1 IS NOT NULL AND kittingShort1.paintedPart = 1) OR 
                        (kitting.kittingShort2 IS NOT NULL AND kittingShort2.paintedPart = 1) OR 
                        (kitting.kittingShort3 IS NOT NULL AND kittingShort3.paintedPart = 1) OR 
                        (kitting.kittingShort4 IS NOT NULL AND kittingShort4.paintedPart = 1)")
            ->andWhere('job.cancelledDate IS NULL');

        if ($name) {
            $qb->andWhere("job.name LIKE :name")
                ->setParameter('name', "%" . $name . "%");
        }

        if ($color) {
            $qb->andWhere("(p.color1 = :color AND p.batch1 IS NULL) OR (p.color2 = :color AND p.batch2 IS NULL)")
                ->setParameter('color', $color);
        }

        if ($plannerEstimatedShipDate) {
            $qb->andWhere("job.plannerEstimatedShipDate = :plannerEstimatedShipDate")
                ->setParameter('plannerEstimatedShipDate', new \DateTime($plannerEstimatedShipDate));
        }
        
        $results = $qb->addOrderBy("job.plannerEstimatedShipDate", "ASC")
            ->getQuery()
            ->getResult();

        return $results;
    }

    public function findJobsInBatch($parameters)
    {
        $name                       = $parameters['name'];
        $color                      = $parameters['color'];
        $batch                      = $parameters['batch'];
        $v2PoNumber                 = $parameters['v2_po_number'];
        $plannerEstimatedShipDate   = $parameters['planner_esd'];

        $qb = $this->createQueryBuilder('p')
            ->addSelect('CASE WHEN (batch1.id IS NOT NULL OR batch2.id IS NOT NULL) THEN 1 ELSE 2 END AS HIDDEN ordering1')
            ->join('p.job', 'job')
            ->leftJoin('p.batch1', 'batch1')
            ->leftJoin('p.batch2', 'batch2')
            ->where('p.id > 0')
            ->andWhere("(p.location IS NULL OR p.location LIKE 'B_%')")
            ->andWhere('job.cancelledDate IS NULL');

        if ($name) {
            $qb->andWhere("job.name LIKE :name")
                ->setParameter('name', "%" . $name . "%");
        }

        if ($color) {
            $qb->andWhere("batch1.color = :color OR batch2.color = :color")
                ->setParameter('color', $color);
        }

        if ($batch) {
            $qb->andWhere("batch1.id = :batch OR batch2.id = :batch")
                ->setParameter('batch', $batch);
        }

        if ($v2PoNumber) {
            $qb->andWhere("batch1.v2PoNumber = :v2PoNumber or batch2.v2PoNumber = :v2PoNumber")
                ->setParameter('v2PoNumber', $v2PoNumber);
        }

        if ($plannerEstimatedShipDate) {
            $qb->andWhere("job.plannerEstimatedShipDate = :plannerEstimatedShipDate")
                ->setParameter('plannerEstimatedShipDate', new \DateTime($plannerEstimatedShipDate));
        }
        
        $results = $qb->addOrderBy("ordering1", "ASC")
            ->addOrderBy("batch1.id", "ASC")
            ->addOrderBy("batch2.id", "ASC")
            ->addOrderBy("p.color1", "ASC")
            ->addOrderBy("p.color2", "ASC")
            ->addOrderBy("job.plannerEstimatedShipDate", "ASC")
            ->getQuery()
            ->getResult();

        return $results;
    }

}

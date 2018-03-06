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
            SELECT color_1 AS color, count(job_id) AS num_jobs, sum(quantity) AS sum_qty, min(planner_estimated_ship_date) AS planner_esd 
            FROM paint p
            JOIN job ON job.id = p.job_id
            WHERE color_1 IS NOT NULL
            GROUP BY color_1
                UNION
            SELECT color_2 AS color, count(job_id), sum(quantity), min(planner_estimated_ship_date) AS planner_esd 
            FROM paint p
            JOIN job ON job.id = p.job_id
            WHERE color_2 IS NOT NULL
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
            ->Join('job.kitting', 'kitting')
            ->leftJoin('kitting.kittingShort1', 'kittingShort1')
            ->leftJoin('kitting.kittingShort2', 'kittingShort2')
            ->leftJoin('kitting.kittingShort3', 'kittingShort3')
            ->leftJoin('kitting.kittingShort4', 'kittingShort4')
            ->where('p.id > 0');

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
        $plannerEstimatedShipDate   = $parameters['planner_esd'];

        $qb = $this->createQueryBuilder('p')
            ->join('p.job', 'job')
            ->leftJoin('p.batch1', 'batch1')
            ->leftJoin('p.batch2', 'batch2')
            ->where('p.id > 0');

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

        if ($plannerEstimatedShipDate) {
            $qb->andWhere("job.plannerEstimatedShipDate = :plannerEstimatedShipDate")
                ->setParameter('plannerEstimatedShipDate', new \DateTime($plannerEstimatedShipDate));
        }
        
        $results = $qb->addOrderBy("job.plannerEstimatedShipDate", "ASC")
            ->getQuery()
            ->getResult();

        return $results;
    }

}

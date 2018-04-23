<?php

namespace V2\MainBundle\Repository;

/**
 * KittingShortRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class KittingShortRepository extends \Doctrine\ORM\EntityRepository
{
    public function findSupplyChainParts($parameters)
    {
        $partNumber = $parameters['part_number'];
        $vendor     = $parameters['vendor'];

        $qb = $this->createQueryBuilder('ks')
            ->leftJoin('ks.kitting', 'kitting')
            ->leftJoin('kitting.job', 'job')
            ->leftJoin('job.scheduling', 'scheduling')
            ->where('ks.receivedDate IS NULL')
            ->andWhere("(ks.vendor IS NULL or UPPER(ks.vendor) != 'V2')")
            ->andWhere("job.cancelledDate IS NULL");

        if ($partNumber) {
            $qb->andWhere("ks.partNumber LIKE :partNumber")
                ->setParameter('partNumber', "%" . $partNumber . "%");
        }

        if ($vendor) {
            $qb->andWhere("ks.vendor LIKE :vendor")
                ->setParameter('vendor', "%" . $vendor . "%");
        }

        $results = $qb
            ->addOrderBy("ks.vendor", "ASC")
            ->addOrderBy("ks.vendorPoNumber", "ASC")
            ->addOrderBy("ks.dateNeeded", "ASC")
            ->addOrderBy("job.plannerEstimatedShipDate", "ASC")
            ->getQuery()
            ->getResult();

        return $results;
    }

    public function findReceiverParts($parameters)
    {
        $partNumber = $parameters['part_number'];
        $vendorPoNumber = $parameters['vendor_po_number'];

        $qb = $this->createQueryBuilder('ks')
            ->addSelect('CASE WHEN ks.estimatedDeliveryDate IS NOT NULL THEN 1 ELSE 2 END AS HIDDEN ordering1')
            ->addSelect('CASE WHEN ks.dateNeeded IS NOT NULL THEN 1 ELSE 2 END AS HIDDEN ordering2')
            ->leftJoin('ks.kitting', 'kitting')
            ->leftJoin('kitting.job', 'job')
            ->leftJoin('job.scheduling', 'scheduling')
            ->where("ks.receivedDate IS NULL")
            ->andWhere("job.cancelledDate IS NULL");

        if ($partNumber) {
            $qb->andWhere("ks.partNumber LIKE :partNumber")
                ->setParameter('partNumber', "%" . $partNumber . "%");
        }

        if ($vendorPoNumber) {
            $qb->andWhere("ks.vendorPoNumber LIKE :vendorPoNumber")
                ->setParameter('vendorPoNumber', "%" . $vendorPoNumber . "%");
        }

        $results = $qb
            ->addOrderBy("ordering1", "ASC")
            ->addOrderBy("ks.estimatedDeliveryDate", "ASC")
            ->addOrderBy("ordering2", "ASC")
            ->addOrderBy("ks.dateNeeded", "ASC")
            ->addOrderBy("job.plannerEstimatedShipDate", "ASC")
            ->getQuery()
            ->getResult();

        return $results;
    }

    public function findManufacturerParts($parameters)
    {
        $dateNeededFrom = $parameters['date_needed_from'];
        $dateNeededTo   = $parameters['date_needed_to'];

        $qb = $this->createQueryBuilder('ks')
            ->leftJoin('ks.kitting', 'kitting')
            ->leftJoin('kitting.job', 'job')
            ->leftJoin('job.scheduling', 'scheduling')
            ->where("ks.modDoneDate IS NULL")
            ->andWhere("UPPER(ks.vendor) = 'V2'")
            ->andWhere("((kitting.filledCompletely IS NOT NULL AND kitting.filledCompletely = 0) OR kitting.filledCompletely IS NULL)")
            ->andWhere("job.cancelledDate IS NULL");

        if ($dateNeededFrom) {
            $qb->andWhere('ks.dateNeeded >= :dateNeededFrom')
                ->setParameter('dateNeededFrom', new \DateTime($dateNeededFrom));
        }

        if ($dateNeededTo) {
            $qb->andWhere('ks.dateNeeded <= :dateNeededTo')
                ->setParameter('dateNeededTo', new \DateTime($dateNeededTo . " 23:59:59"));
        }

        $results = $qb
        // ->addOrderBy("scheduling.priority", "DESC")
            ->addOrderBy("ks.estimatedDeliveryDate", "ASC")
            ->addOrderBy("job.plannerEstimatedShipDate", "ASC")
            ->getQuery()
            ->getResult();

        return $results;
    }
}

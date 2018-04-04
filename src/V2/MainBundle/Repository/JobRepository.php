<?php

namespace V2\MainBundle\Repository;

use V2\MainBundle\Entity\BuildLocation;

/**
 * JobRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class JobRepository extends \Doctrine\ORM\EntityRepository
{
    public function getJobStats()
    {
        $qb = $this->createQueryBuilder('j')
            ->select('COUNT(j.id) as total')
            ->leftJoin('j.kitting', 'kitting')
            ->leftJoin('j.bom', 'bom')
            ->leftJoin('j.shipping', 'shipping')
            ->leftJoin('j.scheduling', 'scheduling');
        
        $results = $qb->getQuery()
            ->getResult();

        return $results;
    }

    public function getScheduledJobs(BuildLocation $buildLocation, $startDate, $endDate)
    {
        $qb = $this->createQueryBuilder('j')
            ->select('COUNT(j.id) as total')
            ->leftJoin('j.kitting', 'kitting')
            ->leftJoin('j.bom', 'bom')
            ->leftJoin('j.shipping', 'shipping')
            ->leftJoin('j.scheduling', 'scheduling')
            ->where('j.buildLocation = :buildLocation')
            ->setParameter('buildLocation', $buildLocation)
            ->andWhere('j.estimatedShipDate >= :startDate')
            ->setParameter('startDate', $startDate)
            ->andWhere('j.estimatedShipDate <= :endDate')
            ->setParameter('endDate', $endDate);

        $results = $qb->getQuery()
            ->getResult();

        if (count($results) <= 0) {
            return 0;
        }

        return $results[0];
    }

    // Gets the number of jobs shipped by a specific number of days
    public function getJobShippedByDays($numDays)
    {
        $qb = $this->createQueryBuilder('j')
            ->select('COUNT(j.id) as total')
            ->leftJoin('j.shipping', 'shipping')
            ->where('shipping.shipDate > :shipDate')
            ->setParameter('shipDate', date('Y-m-d', strtotime("-$numDays days")));
        
        $results = $qb->getQuery()
            ->getResult();
        if (count($results) <= 0) {
            return 0;
        }
        return $results[0];
    }

    public function findAllJobs($parameters)
    {
        $seeAll                     = $parameters['see_all'];
        $name                       = $parameters['name'];
        $salesOrder                 = $parameters['sales_order'];
        $plannerEstimatedShipDate   = $parameters['planner_esd'];

        $qb         = $this->createQueryBuilder('j')
            ->join('j.scheduling', 'scheduling')
            ->leftJoin('j.shipping', 'shipping')
            ->where('j.id > 0');

        if ($name) {
            $qb->andWhere("j.name LIKE :name")
                ->setParameter('name', "%" . $name . "%");
        }

        if ($salesOrder) {
            $qb->andWhere("j.salesOrder LIKE :salesOrder")
                ->setParameter('salesOrder', "%" . $salesOrder . "%");
        }

        if ($plannerEstimatedShipDate) {
            $qb->andWhere("j.plannerEstimatedShipDate = :plannerEstimatedShipDate")
                ->setParameter('plannerEstimatedShipDate', new \DateTime($plannerEstimatedShipDate));
        }
        
        if ($seeAll === 0) {
            $qb->andWhere("j.plannerEstimatedShipDate IS NULL");
        }

        $results = $qb->addOrderBy("scheduling.priority", "DESC")
            ->addOrderBy("j.createTime", "ASC")
            ->addOrderBy("shipping.shipDate", "ASC")
            ->addOrderBy("shipping.secondShipDate", "ASC")
            ->addOrderBy("j.plannerEstimatedShipDate", "ASC")
            ->getQuery()
            ->getResult();

        return $results;
    }

    public function findByJobName($name)
    {
        $qb         = $this->createQueryBuilder('j');
        $results    = $qb->where("upper(j.name) LIKE :name")
            ->setParameter('name', "%" . strtoupper($name) . "%")
            ->getQuery()
            ->getResult();

        return $results;
    }

    public function findBomBuilderJobs($parameters)
    {
        $name       = $parameters['name'];
        $salesOrder = $parameters['sales_order'];

        $qb = $this->createQueryBuilder('j')
            ->join('j.scheduling', 'scheduling')
            ->join('j.bom', 'bom')
            ->where("bom.issuedDate IS NULL")
            ->orWhere("j.manufacturingOrder IS NULL")
            ->orWhere("bom.serialsGeneratedDate IS NULL");

        if ($name) {
            $qb->andWhere("j.name LIKE :name")
                ->setParameter('name', "%" . $name . "%");
        }

        if ($salesOrder) {
            $qb->andWhere("j.salesOrder LIKE :salesOrder")
                ->setParameter('salesOrder', "%" . $salesOrder . "%");
        }

        $results = $qb->addOrderBy("scheduling.priorityBomBuilder", "DESC")
            ->addOrderBy("j.plannerEstimatedShipDate", "ASC")
            ->getQuery()
            ->getResult();

        return $results;
    }

    public function findKitterJobs($parameters)
    {
        $name       = $parameters['name'];
        $salesOrder = $parameters['sales_order'];

        $qb = $this->createQueryBuilder('j');
        $qb->join('j.kitting', 'kitting')
            ->join('j.scheduling', 'scheduling')
            ->where("kitting.filledCompletely IS NULL");

        if ($name) {
            $qb->andWhere("j.name LIKE :name")
                ->setParameter('name', "%" . $name . "%");
        }

        if ($salesOrder) {
            $qb->andWhere("j.salesOrder LIKE :salesOrder")
                ->setParameter('salesOrder', "%" . $salesOrder . "%");
        }
        
        $results = $qb->addOrderBy("kitting.filledCompletely")
            ->addOrderBy("scheduling.priorityKitter", "DESC")
            ->addOrderBy("j.plannerEstimatedShipDate", "ASC")
            ->getQuery()
            ->getResult();

        return $results;
    }

    public function findReceiverJobs($parameters)
    {
        $partNumber     = $parameters['part_number'];
        $vendorPoNumber = $parameters['vendor_po_number'];

        $qb = $this->createQueryBuilder('j')
            ->join('j.kitting', 'kitting')
            ->join('j.scheduling', 'scheduling')
            ->leftJoin('kitting.kittingShort1', 'kittingShort1')
            ->leftJoin('kitting.kittingShort2', 'kittingShort2')
            ->leftJoin('kitting.kittingShort3', 'kittingShort3')
            ->leftJoin('kitting.kittingShort4', 'kittingShort4')
            ->where("kitting.filledCompletely = 0")
            ->andWhere("(kitting.kittingShort1 IS NOT NULL AND kittingShort1.receivedDate IS NULL) OR 
                        (kitting.kittingShort2 IS NOT NULL AND kittingShort2.receivedDate IS NULL) OR 
                        (kitting.kittingShort3 IS NOT NULL AND kittingShort3.receivedDate IS NULL) OR 
                        (kitting.kittingShort4 IS NOT NULL AND kittingShort4.receivedDate IS NULL)");

        if ($partNumber) {
            $qb->andWhere("kittingShort1.partNumber LIKE :partNumber OR 
                           kittingShort2.partNumber LIKE :partNumber OR 
                           kittingShort3.partNumber LIKE :partNumber OR 
                           kittingShort4.partNumber LIKE :partNumber")
                ->setParameter('partNumber', "%" . $partNumber . "%");
        }

        if ($vendorPoNumber) {
            $qb->andWhere("kittingShort1.vendorPoNumber = :vendorPoNumber OR 
                           kittingShort2.vendorPoNumber = :vendorPoNumber OR 
                           kittingShort3.vendorPoNumber = :vendorPoNumber OR 
                           kittingShort4.vendorPoNumber = :vendorPoNumber")
                ->setParameter('vendorPoNumber', $vendorPoNumber);
        }

        $results = $qb->addOrderBy("scheduling.priority", "DESC")
            ->addOrderBy("j.plannerEstimatedShipDate", "ASC")
            ->getQuery()
            ->getResult();

        return $results;
    }

    public function findManufacturerJobs($parameters)
    {
        $dateNeededFrom = $parameters['date_needed_from'];
        $dateNeededTo   = $parameters['date_needed_to'];

        $qb = $this->createQueryBuilder('j')
            ->join('j.kitting', 'kitting')
            ->join('j.scheduling', 'scheduling')
            ->leftJoin('kitting.kittingShort1', 'kittingShort1')
            ->leftJoin('kitting.kittingShort2', 'kittingShort2')
            ->leftJoin('kitting.kittingShort3', 'kittingShort3')
            ->leftJoin('kitting.kittingShort4', 'kittingShort4')
            ->where("kitting.filledCompletely = 0")
            ->andWhere("(kitting.kittingShort1 IS NOT NULL AND kittingShort1.modDoneDate IS NULL) OR 
                        (kitting.kittingShort2 IS NOT NULL AND kittingShort2.modDoneDate IS NULL) OR 
                        (kitting.kittingShort3 IS NOT NULL AND kittingShort3.modDoneDate IS NULL) OR 
                        (kitting.kittingShort4 IS NOT NULL AND kittingShort4.modDoneDate IS NULL)");
        
        if ($dateNeededFrom) {
            $qb->andWhere('kittingShort1.dateNeeded >= :dateNeededFrom')
                ->setParameter('dateNeededFrom', new \DateTime($dateNeededFrom));
        }

        if ($dateNeededTo) {
            $qb->andWhere('kittingShort1.dateNeeded <= :dateNeededTo')
                ->setParameter('dateNeededTo', new \DateTime($dateNeededTo . " 23:59:59"));
        }

        $results = $qb->addOrderBy("scheduling.priority", "DESC")
            ->addOrderBy("j.plannerEstimatedShipDate", "ASC")
            ->getQuery()
            ->getResult();

        return $results;
    }

    public function findSupplyChainJobs($parameters)
    {
        $partNumber = $parameters['part_number'];
        $vendor     = $parameters['vendor'];

        $qb = $this->createQueryBuilder('j')
            ->join('j.kitting', 'kitting')
            ->join('j.scheduling', 'scheduling')
            ->leftJoin('kitting.kittingShort1', 'kittingShort1')
            ->leftJoin('kitting.kittingShort2', 'kittingShort2')
            ->leftJoin('kitting.kittingShort3', 'kittingShort3')
            ->leftJoin('kitting.kittingShort4', 'kittingShort4')
            ->where("kitting.filledCompletely = 0");

        if ($partNumber) {
            $qb->andWhere("kittingShort1.partNumber LIKE :partNumber OR 
                           kittingShort2.partNumber LIKE :partNumber OR 
                           kittingShort3.partNumber LIKE :partNumber OR 
                           kittingShort4.partNumber LIKE :partNumber")
                ->setParameter('partNumber', "%" . $partNumber . "%");
        }

        if ($vendor) {
            $qb->andWhere("kittingShort1.vendor LIKE :vendor OR 
                           kittingShort2.vendor LIKE :vendor OR 
                           kittingShort3.vendor LIKE :vendor OR 
                           kittingShort4.vendor LIKE :vendor")
                ->setParameter('vendor', "%" . $vendor . "%");
        }

        $results = $qb->addOrderBy("scheduling.priority", "DESC")
            ->addOrderBy("j.plannerEstimatedShipDate", "ASC")
            ->addOrderBy("kittingShort1.dateNeeded", "ASC")
            ->addOrderBy("kittingShort2.dateNeeded", "ASC")
            ->addOrderBy("kittingShort3.dateNeeded", "ASC")
            ->addOrderBy("kittingShort4.dateNeeded", "ASC")
            ->getQuery()
            ->getResult();

        return $results;
    }

    public function findMacProductionJobs($parameters)
    {
        $salesOrder                 = $parameters['sales_order'];
        $plannerEstimatedShipDate   = $parameters['planner_esd'];

        $qb = $this->createQueryBuilder('j')
            ->join('j.scheduling', 'scheduling')
            ->join('j.kitting', 'kitting')
            ->join('j.buildLocation', 'buildLocation')
            ->leftJoin('kitting.kittingShort1', 'kittingShort1')
            ->leftJoin('kitting.kittingShort2', 'kittingShort2')
            ->leftJoin('kitting.kittingShort3', 'kittingShort3')
            ->leftJoin('kitting.kittingShort4', 'kittingShort4')
            ->where("kitting.filledCompletely = 1")
            ->andWhere("buildLocation.name = 'MAC'")
            ->andWhere("scheduling.completionDate IS NULL");

        if ($salesOrder) {
            $qb->andWhere("j.salesOrder LIKE :salesOrder")
                ->setParameter('salesOrder', "%" . $salesOrder . "%");
        }

        if ($plannerEstimatedShipDate) {
            $qb->andWhere("j.plannerEstimatedShipDate = :plannerEstimatedShipDate")
                ->setParameter('plannerEstimatedShipDate', new \DateTime($plannerEstimatedShipDate));
        }

        $results = $qb->addOrderBy("scheduling.priorityMacProduction", "DESC")
            ->addOrderBy("j.plannerEstimatedShipDate", "ASC")
            ->getQuery()
            ->getResult();

        return $results;
    }

    public function findV2ProductionJobs($parameters)
    {
        $salesOrder                 = $parameters['sales_order'];
        $plannerEstimatedShipDate   = $parameters['planner_esd'];

        $qb = $this->createQueryBuilder('j')
            ->join('j.scheduling', 'scheduling')
            ->join('j.kitting', 'kitting')
            ->leftJoin('kitting.kittingShort1', 'kittingShort1')
            ->leftJoin('kitting.kittingShort2', 'kittingShort2')
            ->leftJoin('kitting.kittingShort3', 'kittingShort3')
            ->leftJoin('kitting.kittingShort4', 'kittingShort4')
            ->where("j.macPurchaseOrder IS NULL")
            ->andWhere("scheduling.completionDate IS NULL");
            
        if ($salesOrder) {
            $qb->andWhere("j.salesOrder LIKE :salesOrder")
                ->setParameter('salesOrder', "%" . $salesOrder . "%");
        }

        if ($plannerEstimatedShipDate) {
            $qb->andWhere("j.plannerEstimatedShipDate = :plannerEstimatedShipDate")
                ->setParameter('plannerEstimatedShipDate', new \DateTime($plannerEstimatedShipDate));
        }

        $results = $qb->addOrderBy("scheduling.priorityV2Production", "DESC")
            ->addOrderBy("j.plannerEstimatedShipDate", "ASC")
            ->getQuery()
            ->getResult();

        return $results;
    }

    public function findShipperJobs()
    {
        $qb = $this->createQueryBuilder('j')
            ->join('j.scheduling', 'scheduling')
            ->leftJoin('j.shipping', 'shipping')
            ->where('scheduling.completionDate IS NOT NULL')
            ->andWhere("(shipping.shipDate IS NULL) OR (shipping.isComplete = 1 AND shipping.secondShipDate IS NULL)");

        $results = $qb->addOrderBy("scheduling.priorityShipper", "DESC")
            ->addOrderBy("j.plannerEstimatedShipDate", "ASC")
            ->getQuery()
            ->getResult();

        return $results;
    }

    public function findSchedulerJobs($parameters)
    {
        $name               = $parameters['name'];
        $esd                = $parameters['esd'];
        $filledCompletely   = $parameters['filled_completely'];
        $nonShipped         = $parameters['non_shipped'];

        $qb = $this->createQueryBuilder('j')
            ->join('j.kitting', 'kitting')
            ->join('j.scheduling', 'scheduling')
            ->leftJoin('j.shipping', 'shipping')
            ->where('j.id > 0');

        if ($name) {
            $qb->andWhere("j.name LIKE :name")
                ->setParameter('name', "%" . $name . "%");
        }

        if ($esd) {
            $qb->andWhere("j.estimatedShipDate = :estimatedShipDate")
                ->setParameter('estimatedShipDate', new \DateTime($esd));
        }

        if ($filledCompletely) {
            $qb->andWhere("kitting.filledCompletely = :filledCompletely")
                ->setParameter('filledCompletely', $filledCompletely);
        }

        if ($nonShipped) {
            $qb->andWhere("shipping.shipDate IS NULL");
        }

        $results = $qb->addOrderBy("j.plannerEstimatedShipDate", "ASC")
            ->getQuery()
            ->getResult();

        return $results;
    }

}

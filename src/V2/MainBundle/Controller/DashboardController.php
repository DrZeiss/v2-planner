<?php

namespace V2\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Doctrine\ORM\EntityManager;

class DashboardController extends Controller
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="dashboard")
     */
    public function indexAction()
    {
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 week'));
        $v2Location = $this->em->getRepository('V2MainBundle:BuildLocation')->find(1); // V2
        $scheduledV2Jobs = $this->em->getRepository('V2MainBundle:Job')->getScheduledJobs($v2Location, $startDate, $endDate);

        $macLocation = $this->em->getRepository('V2MainBundle:BuildLocation')->find(2); // MAC
        $scheduledMacJobs = $this->em->getRepository('V2MainBundle:Job')->getScheduledJobs($macLocation, $startDate, $endDate);

        $jobStats = $this->em->getRepository('V2MainBundle:Job')->getJobStats()[0];

        $jobsShippedToday = $this->em->getRepository('V2MainBundle:Job')->getJobShippedByDays(1);
        $jobsShippedThisWeek = $this->em->getRepository('V2MainBundle:Job')->getJobShippedByDays(7);
        $jobsShippedThisMonth = $this->em->getRepository('V2MainBundle:Job')->getJobShippedByDays(30);

        return $this->render('dashboard/index.html.twig', array(
            'startDate'             =>  $startDate,
            'endDate'               =>  $endDate,
            'total'                 =>  $jobStats['total'],
            'scheduledV2Jobs'       =>  $scheduledV2Jobs['total'],
            'scheduledMacJobs'      =>  $scheduledMacJobs['total'],
            'jobsShippedToday'      =>  $jobsShippedToday['total'],
            'jobsShippedThisWeek'   =>  $jobsShippedThisWeek['total'],
            'jobsShippedThisMonth'  =>  $jobsShippedThisMonth['total'],
            // 'kitter'          =>  $jobStats['kitter'],
            // 'receiver'        =>  $jobStats['receiver'],
            // 'manufacturer'    =>  $jobStats['manufacturer'],
            // 'supplyChain'     =>  $jobStats['supply_chain'],
            // 'macProduction'   =>  $jobStats['mac_production'],
            // 'v2Production'    =>  $jobStats['v2_production'],
            // 'scheduler'       =>  $jobStats['scheduler'],
        ));
    }
}

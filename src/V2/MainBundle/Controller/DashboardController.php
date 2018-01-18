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
        $jobStats = $this->em->getRepository('V2MainBundle:Job')->getJobStats()[0];

        return $this->render('dashboard/index.html.twig', array(
            'total'           =>  $jobStats['total'],
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

<?php

namespace V2\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Doctrine\ORM\EntityManager;
use V2\MainBundle\Entity\Job;
use V2\MainBundle\Entity\Bom;
use V2\MainBundle\Entity\Kitting;
use V2\MainBundle\Entity\KittingShort;
use V2\MainBundle\Entity\Shipping;
use V2\MainBundle\Entity\Scheduling;
use V2\MainBundle\Form\JobType;
use V2\MainBundle\Form\EditJobType;
use V2\MainBundle\Form\BomType;
use V2\MainBundle\Form\KittingType;
use V2\MainBundle\Form\ShippingType;
use V2\MainBundle\Form\SchedulingType;

/**
 * @Route("/jobs")
 */
class JobController extends Controller
{
    protected $em;
    protected $jobRepository;
    protected $bomRepository;
    protected $kittingRepository;
    protected $kittingShortRepository;
    protected $shippingRepository;
    protected $schedulingRepository;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->jobRepository = $this->em->getRepository('V2MainBundle:Job');
        $this->bomRepository = $this->em->getRepository('V2MainBundle:Bom');
        $this->kittingRepository = $this->em->getRepository('V2MainBundle:Kitting');
        $this->kittingShortRepository = $this->em->getRepository('V2MainBundle:KittingShort');
        $this->shippingRepository = $this->em->getRepository('V2MainBundle:Shipping');
        $this->schedulingRepository = $this->em->getRepository('V2MainBundle:Scheduling');
    }

    /**
     * @Route(name="jobs")
     */
    public function listJobs()
    {
        echo "404!";
        exit;
    }

    /**
     * @Route("/all", name="all")
     */
    public function listAllJobs()
    {
        $jobs           = $this->jobRepository->findAll();

        return $this->render('job/list_all.html.twig', array(
            'jobs'      =>  $jobs,
        ));
    }

    /**
     * @Route("/kitter", name="kitter")
     */
    public function listKitterJobs()
    {
        $jobs           = $this->jobRepository->findKitterJobs();

        return $this->render('job/list_kitter.html.twig', array(
            'jobs'      =>  $jobs,
        ));
    }

    /**
     * @Route("/receiver", name="receiver")
     */
    public function listReceiverJobs()
    {
        $jobs           = $this->jobRepository->findReceiverJobs();

        return $this->render('job/list_receiver.html.twig', array(
            'jobs'      =>  $jobs,
        ));
    }

    /**
     * @Route("/manufacturer", name="manufacturer")
     */
    public function listManufacturerJobs()
    {
        $jobs           = $this->jobRepository->findManufacturerJobs();

        return $this->render('job/list_manufacturer.html.twig', array(
            'jobs'      =>  $jobs,
        ));
    }

    /**
     * @Route("/supplyChain", name="supply_chain")
     */
    public function listSupplyChainJobs()
    {
        $jobs           = $this->jobRepository->findSupplyChainJobs();

        return $this->render('job/list_supply_chain.html.twig', array(
            'jobs'      =>  $jobs,
        ));
    }

    /**
     * @Route("/macProduction", name="mac_production")
     */
    public function listMacProductionJobs()
    {
        $jobs           = $this->jobRepository->findMacProductionJobs();

        return $this->render('job/list_mac_production.html.twig', array(
            'jobs'      =>  $jobs,
        ));
    }

    /**
     * @Route("/v2Production", name="v2_production")
     */
    public function listV2ProductionJobs()
    {
        $jobs           = $this->jobRepository->findV2ProductionJobs();

        return $this->render('job/list_v2_production.html.twig', array(
            'jobs'      =>  $jobs,
        ));
    }

    /**
     * @Route("/scheduler", name="scheduler")
     */
    public function listSchedulerJobs()
    {
        $jobs           = $this->jobRepository->findSchedulerJobs();

        return $this->render('job/list_scheduler.html.twig', array(
            'jobs'      =>  $jobs,
        ));
    }

    /**
     * @Route("/create", name="create_job")
     */
    public function createJob(Request $request)
    {
        $job = new Job();
        $form = $this->createForm(JobType::class, $job);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                try {
                    $job->setUpdateBy(1); // TODO: Add users
                    $job->setUpdateTime(new \DateTime());

                    $this->em->persist($job);
                    $this->em->flush();                    
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error while updating!');
                    return $this->redirect($request->getUri());
                }

                $this->addFlash('success', 'Job created!');
                return $this->redirect($this->generateUrl('jobs'));
            }
        }

        return $this->render('job/create.html.twig', array(
            'job'       =>  $job,
            'form'      =>  $form->createView(),
        ));

    }

    /**
     * @Route("/{jobId}/edit", name="edit_job")
     */
    public function editJob(Request $request, $jobId)
    {
        $job = $this->jobRepository->find($jobId);

        $form = $this->createForm(EditJobType::class, $job);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            
            if ($form->isValid()) {
                try {
                    $job->setUpdateBy(1); // TODO: Add users
                    $job->setUpdateTime(new \DateTime());

                    $this->em->persist($job);
                    $this->em->flush();                    
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error while updating!');
                    return $this->redirect($request->getUri());
                }

                $this->addFlash('success', 'Job updated');
                return $this->redirect($this->generateUrl('jobs'));
            }
        }

        return $this->render('job/edit.html.twig', array(
            'job'       => $job,
            'form'      => $form->createView(),
        ));        
    }

    /**
     * @Route("/bom/{jobId}/edit", name="edit_bom")
     */
    public function createBom(Request $request, $jobId)
    {
        $job = $this->jobRepository->find($jobId);
        $bom = $this->bomRepository->findOneBy(array('job' => $job));
        if (!$bom) {
            $bom = new Bom();
            $bom->setJob($job);
        }
        $form = $this->createForm(BomType::class, $bom);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                try {
                    $bom->setUpdateTime(new \DateTime());

                    $this->em->persist($bom);
                    $this->em->flush();                    
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error while updating!');
                    return $this->redirect($request->getUri());
                }

                $this->addFlash('success', 'Bom updated!');
                return $this->redirect($this->generateUrl('jobs'));
            }
        }

        return $this->render('job/edit_bom.html.twig', array(
            'bom'       =>  $bom,
            'form'      =>  $form->createView(),
        ));

    }

    /**
     * @Route("/job/{jobId}/editName", name="edit_job_name")
     */
    public function editJobName(Request $request, $jobId)
    {
        $name = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }

        $job->setName($name);
        $job->setUpdateTime(new \DateTime());
        $job->setUpdatedBy($this->getUser());
        $this->em->persist($job);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/job/{jobId}/editSalesOrder", name="edit_job_sales_order")
     */
    public function editJobSalesOrder(Request $request, $jobId)
    {
        $salesOrder = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }

        $job->setSalesOrder($salesOrder);
        $job->setUpdateTime(new \DateTime());
        $job->setUpdatedBy($this->getUser());
        $this->em->persist($job);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/job/{jobId}/editEstimatedShipDate", name="edit_job_estimated_ship_date")
     */
    public function editJobEstimatedShipDate(Request $request, $jobId)
    {
        $estimatedShipDate = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }

        $job->setEstimatedShipDate(new \DateTime($estimatedShipDate));
        $job->setUpdateTime(new \DateTime());
        $job->setUpdatedBy($this->getUser());
        $this->em->persist($job);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/job/{jobId}/editType", name="edit_job_type")
     */
    public function editJobType(Request $request, $jobId)
    {
        $type = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }

        $job->setType($type);
        $job->setUpdateTime(new \DateTime());
        $job->setUpdatedBy($this->getUser());
        $this->em->persist($job);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/job/{jobId}/editModel", name="edit_job_model")
     */
    public function editJobModel(Request $request, $jobId)
    {
        $model = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }

        $job->setModel($model);
        $job->setUpdateTime(new \DateTime());
        $job->setUpdatedBy($this->getUser());
        $this->em->persist($job);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/job/{jobId}/editQuantity", name="edit_job_quantity")
     */
    public function editJobQuantity(Request $request, $jobId)
    {
        $quantity = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }

        $job->setQuantity($quantity);
        $job->setUpdateTime(new \DateTime());
        $job->setUpdatedBy($this->getUser());
        $this->em->persist($job);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/job/{jobId}/editManufacturingOrder", name="edit_job_manufacturing_order")
     */
    public function editJobManufacturingOrder(Request $request, $jobId)
    {
        $manufacturingOrder = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }

        $job->setManufacturingOrder($manufacturingOrder);
        $job->setUpdateTime(new \DateTime());
        $job->setUpdatedBy($this->getUser());
        $this->em->persist($job);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/job/{jobId}/editMacPurchaseOrder", name="edit_job_mac_purchase_order")
     */
    public function editJobMacPurchaseOrder(Request $request, $jobId)
    {
        $macPurchaseOrder = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }

        $job->setMacPurchaseOrder($macPurchaseOrder);
        $job->setUpdateTime(new \DateTime());
        $job->setUpdatedBy($this->getUser());
        $this->em->persist($job);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/job/{jobId}/editBuildLocation", name="edit_job_build_location")
     */
    public function editJobBuildLocation(Request $request, $jobId)
    {
        $buildLocationId = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }

        $buildLocation = $this->em->getRepository('V2MainBundle:BuildLocation')->findOneBy(array('id' => $buildLocationId));
        $job->setBuildLocation($buildLocation);
        $job->setUpdateTime(new \DateTime());
        $job->setUpdatedBy($this->getUser());
        $this->em->persist($job);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/job/{jobId}/editPlannerEstimatedShipDate", name="edit_job_planner_estimated_ship_date")
     */
    public function editJobPlannerEstimatedShipDate(Request $request, $jobId)
    {
        $plannerEstimatedShipDate = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }

        $job->setPlannerEstimatedShipDate(new \DateTime($plannerEstimatedShipDate));
        $job->setUpdateTime(new \DateTime());
        $job->setUpdatedBy($this->getUser());
        $this->em->persist($job);
        $this->em->flush();

        return $this->json(array('status' => 'success', 'weekNum' => date('W', strtotime($plannerEstimatedShipDate))));
    }

    /**
     * @Route("/bom/{jobId}/editSerialsGeneratedDate", name="edit_bom_serials_generated_date")
     */
    public function editBomSerialsGeneratedDate(Request $request, $jobId)
    {
        $serialsGeneratedDate = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }
        $bom = $this->bomRepository->findOneBy(array('job' => $job));
        if (!$bom) {
            $bom = new Bom();
            $bom->setJob($job);
        }
        $bom->setSerialsGeneratedDate(new \DateTime($serialsGeneratedDate));
        $this->em->persist($bom);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/bom/{jobId}/editIssuedDate", name="edit_bom_issued_date")
     */
    public function editBomIssueDate(Request $request, $jobId)
    {
        $issuedDate = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }
        $bom = $this->bomRepository->findOneBy(array('job' => $job));
        if (!$bom) {
            $bom = new Bom();
            $bom->setJob($job);
        }
        $bom->setIssuedDate(new \DateTime($issuedDate));
        $bom->setIssuedBy($this->getUser());
        $bom->setUpdateTime(new \DateTime());
        $bom->setUpdatedBy($this->getUser());
        $this->em->persist($bom);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/shipping/{jobId}/editIsComplete", name="edit_shipping_is_complete")
     */
    public function editShippingIsComplete(Request $request, $jobId)
    {
        $isComplete = $request->request->get('value');
        
        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }
        $shipping = $this->shippingRepository->findOneBy(array('job' => $job));
        if (!$shipping) {
            $shipping = new Shipping();
            $shipping->setJob($job);
        }
        $shipping->setIsComplete($isComplete);
        $shipping->setUpdateTime(new \DateTime());
        $shipping->setUpdatedBy($this->getUser());
        $this->em->persist($shipping);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/shipping/{jobId}/editShipDate", name="edit_shipping_ship_date")
     */
    public function editShippingShipDate(Request $request, $jobId)
    {
        $shipDate = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }
        $shipping = $this->shippingRepository->findOneBy(array('job' => $job));
        if (!$shipping) {
            $shipping = new Shipping();
            $shipping->setJob($job);
            $shipping->setIsComplete(0);
        }
        $shipping->setShipDate(new \DateTime($shipDate));
        $shipping->setUpdateTime(new \DateTime());
        $shipping->setUpdatedBy($this->getUser());
        $this->em->persist($shipping);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/shipping/{jobId}/editSecondShipDate", name="edit_shipping_second_ship_date")
     */
    public function editShippingSecondShipDate(Request $request, $jobId)
    {
        $secondShipDate = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }
        $shipping = $this->shippingRepository->findOneBy(array('job' => $job));
        if (!$shipping) {
            $shipping = new Shipping();
            $shipping->setJob($job);
        }
        $shipping->setSecondShipDate(new \DateTime($secondShipDate));
        $shipping->setUpdateTime(new \DateTime());
        $shipping->setUpdatedBy($this->getUser());
        $this->em->persist($shipping);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }


    /**
     * @Route("/kitting/{jobId}/editFilledCompletely", name="edit_kitting_filled_completely")
     */
    public function editKittingFilledCompletely(Request $request, $jobId)
    {
        $filledCompletely = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        $kitting = $this->kittingRepository->findOneBy(array('job' => $job));
        if (!$kitting) {
            $kitting = new Kitting();
            $kitting->setJob($job);
        }

        $kitting->setFilledCompletely(($filledCompletely == 'N/A') ? null : $filledCompletely );
        $kitting->setUpdateTime(new \DateTime());
        $kitting->setUpdatedBy($this->getUser());

        $this->em->persist($kitting);
        $this->em->flush();
        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/kitting/{jobId}/editCompletionDate", name="edit_kitting_completion_date")
     */
    public function editKittingCompletionDate(Request $request, $jobId)
    {
        $completionDate = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        $kitting = $this->kittingRepository->findOneBy(array('job' => $job));
        if (!$kitting) {
            $kitting = new Kitting();
            $kitting->setJob($job);
        }

        $kitting->setCompletionDate(new \DateTime($completionDate));
        $kitting->setUpdateTime(new \DateTime());
        $kitting->setUpdatedBy($this->getUser());

        $this->em->persist($kitting);
        $this->em->flush();
        return $this->json(array('status' => 'success'));
    }

    /**
     * It is assumed when a part number is specified for a short, it is usually a new short
     *
     * @Route("/kitting/{jobId}/editShort1/{shortId}", name="edit_kitting_short1")
     */
    public function editKittingShort1(Request $request, $jobId, $shortId)
    {
        $job = $this->jobRepository->find($jobId);
        $partNumber = $request->request->get('value');

        $short1 = $this->kittingShortRepository->findOneBy(array('id' => $shortId));
        if (!$short1) {
            $short1 = new KittingShort();
        }
        $short1->setPartNumber($partNumber);
        $short1->setUpdatedBy($this->getUser());
        $this->em->persist($short1);

        $kitting = $this->kittingRepository->findOneBy(array('job' => $job));
        if (!$kitting) {
            $kitting = new Kitting();
            $kitting->setJob($job);
        }
        $kitting->setKittingShort1($short1);
        $kitting->setUpdateTime(new \DateTime());
        $kitting->setUpdatedBy($this->getUser());

        $this->em->persist($kitting);
        $this->em->flush();
        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/kittingShort/{shortId}/editPaintedPart", name="edit_kitting_short_painted_part")
     */
    public function editKittingShortPaintedPart(Request $request, $shortId)
    {
        $paintedPart = $request->request->get('value');

        $short = $this->kittingShortRepository->findOneBy(array('id' => $shortId));
        if (!$short) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid kitting short"));
        }
        $short->setPaintedPart($paintedPart);
        $short->setUpdateTime(new \DateTime());
        $short->setUpdatedBy($this->getUser());
        $this->em->persist($short);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/kittingShort/{shortId}/editDateNeeded", name="edit_kitting_short_date_needed")
     */
    public function editKittingShortDateNeeded(Request $request, $shortId)
    {
        $dateNeeded = $request->request->get('value');

        $short = $this->kittingShortRepository->findOneBy(array('id' => $shortId));
        if (!$short) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid kitting short"));
        }
        $short->setDateNeeded(new \DateTime($dateNeeded));
        $short->setUpdateTime(new \DateTime());
        $short->setUpdatedBy($this->getUser());
        $this->em->persist($short);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/kitting/{jobId}/editShort2/{shortId}", name="edit_kitting_short2")
     */
    public function editKittingShort2(Request $request, $jobId, $shortId)
    {
        $job = $this->jobRepository->find($jobId);
        $partNumber = $request->request->get('value');

        $short2 = $this->kittingShortRepository->findOneBy(array('id' => $shortId));
        if (!$short2) {
            $short2 = new KittingShort();
        }
        $short2->setPartNumber($partNumber);
        $this->em->persist($short2);

        $kitting = $this->kittingRepository->findOneBy(array('job' => $job));
        if (!$kitting) {
            $kitting = new Kitting();
            $kitting->setJob($job);
        }
        $kitting->setKittingShort2($short2);
        $kitting->setUpdateTime(new \DateTime());
        $kitting->setUpdatedBy($this->getUser());

        $this->em->persist($kitting);
        $this->em->flush();
        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/kitting/{jobId}/editShort3/{shortId}", name="edit_kitting_short3")
     */
    public function editKittingShort3(Request $request, $jobId, $shortId)
    {
        $job = $this->jobRepository->find($jobId);
        $partNumber = $request->request->get('value');

        $short3 = $this->kittingShortRepository->findOneBy(array('id' => $shortId));
        if (!$short3) {
            $short3 = new KittingShort();
        }
        $short3->setPartNumber($partNumber);
        $this->em->persist($short3);

        $kitting = $this->kittingRepository->findOneBy(array('job' => $job));
        if (!$kitting) {
            $kitting = new Kitting();
            $kitting->setJob($job);
        }
        $kitting->setKittingShort3($short3);
        $kitting->setUpdateTime(new \DateTime());
        $kitting->setUpdatedBy($this->getUser());

        $this->em->persist($kitting);
        $this->em->flush();
        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/kitting/{jobId}/editShort4/{shortId}", name="edit_kitting_short4")
     */
    public function editKittingShort4(Request $request, $jobId, $shortId)
    {
        $job = $this->jobRepository->find($jobId);
        $partNumber = $request->request->get('value');

        $short4 = $this->kittingShortRepository->findOneBy(array('id' => $shortId));
        if (!$short4) {
            $short4 = new KittingShort();
        }
        $short4->setPartNumber($partNumber);
        $this->em->persist($short4);

        $kitting = $this->kittingRepository->findOneBy(array('job' => $job));
        if (!$kitting) {
            $kitting = new Kitting();
            $kitting->setJob($job);
        }
        $kitting->setKittingShort4($short4);
        $kitting->setUpdateTime(new \DateTime());
        $kitting->setUpdatedBy($this->getUser());

        $this->em->persist($kitting);
        $this->em->flush();
        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/kitting/{jobId}/editLocation", name="edit_kitting_location")
     */
    public function editKittingLocation(Request $request, $jobId)
    {
        $location = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        $kitting = $this->kittingRepository->findOneBy(array('job' => $job));
        if (!$kitting) {
            $kitting = new Kitting();
            $kitting->setJob($job);
        }
        $kitting->setLocation($location);
        $kitting->setUpdateTime(new \DateTime());
        $kitting->setUpdatedBy($this->getUser());

        $this->em->persist($kitting);
        $this->em->flush();
        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/kitting/{jobId}/editEpoxy", name="edit_kitting_epoxy")
     */
    public function editKittingEpoxy(Request $request, $jobId)
    {
        $epoxy = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        $kitting = $this->kittingRepository->findOneBy(array('job' => $job));
        if (!$kitting) {
            $kitting = new Kitting();
            $kitting->setJob($job);
        }
        $kitting->setEpoxy($epoxy);
        $kitting->setUpdateTime(new \DateTime());
        $kitting->setUpdatedBy($this->getUser());

        $this->em->persist($kitting);
        $this->em->flush();
        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/kitting/{jobId}/editTube", name="edit_kitting_tube")
     */
    public function editKittingTube(Request $request, $jobId)
    {
        $tube = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        $kitting = $this->kittingRepository->findOneBy(array('job' => $job));
        if (!$kitting) {
            $kitting = new Kitting();
            $kitting->setJob($job);
        }
        $kitting->setTube($tube);
        $kitting->setUpdateTime(new \DateTime());
        $kitting->setUpdatedBy($this->getUser());

        $this->em->persist($kitting);
        $this->em->flush();
        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/kitting/{jobId}/edit", name="edit_kitting")
     */
    public function editKitting(Request $request, $jobId)
    {
        $job = $this->jobRepository->find($jobId);
        $kitting = $this->kittingRepository->findOneBy(array('job' => $job));
        if (!$kitting) {
            $kitting = new Kitting();
            $kitting->setJob($job);
        }
        $form = $this->createForm(KittingType::class, $kitting);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                try {
                    // $kittingShort1 = $form->getData()->getKittingShort1();
                    // $kittingShort1->setKitting($kitting);
                    // $this->em->persist($kittingShort1);
                    
                    $kitting->setUpdatedTime(new \DateTime());
                    $kitting->setUpdateBy($this->getUser());

                    $this->em->persist($kitting);
                    $this->em->flush();                    
                } catch (\Exception $e) {
                    return $this->json(array('status' => 'error', 'msg' => $e->getMessage()));
                }
                return $this->json(array('status' => 'success'));
            }
        }
    }


    /**
     * @Route("/shipping/{jobId}/edit", name="edit_shipping")
     */
    public function createShipping(Request $request, $jobId)
    {
        $job = $this->jobRepository->find($jobId);
        $shipping = $this->shippingRepository->findOneBy(array('job' => $job));
        if (!$shipping) {
            $shipping = new Shipping();
            $shipping->setJob($job);
        }
        $form = $this->createForm(ShippingType::class, $shipping);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                try {
                    $shipping->setUpdateTime(new \DateTime());

                    $this->em->persist($shipping);
                    $this->em->flush();                    
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error while updating!');
                    return $this->redirect($request->getUri());
                }

                $this->addFlash('success', 'Shipping updated!');
                return $this->redirect($this->generateUrl('jobs'));
            }
        }

        return $this->render('job/edit_shipping.html.twig', array(
            'shipping'       =>  $shipping,
            'form'      =>  $form->createView(),
        ));

    }

    /**
     * @Route("/scheduling/{jobId}/edit", name="edit_scheduling")
     */
    public function createScheduling(Request $request, $jobId)
    {
        $job = $this->jobRepository->find($jobId);
        $scheduling = $this->schedulingRepository->findOneBy(array('job' => $job));
        if (!$scheduling) {
            $scheduling = new Scheduling();
            $scheduling->setJob($job);
        }
        $form = $this->createForm(SchedulingType::class, $scheduling);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                try {
                    $scheduling->setUpdateTime(new \DateTime());

                    $this->em->persist($scheduling);
                    $this->em->flush();                    
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error while updating!');
                    return $this->redirect($request->getUri());
                }

                $this->addFlash('success', 'Scheduling updated!');
                return $this->redirect($this->generateUrl('jobs'));
            }
        }

        return $this->render('job/edit_scheduling.html.twig', array(
            'scheduling'    =>  $scheduling,
            'form'          =>  $form->createView(),
        ));

    }

    /**
     * @Route("/kittingShort/{shortId}/editReceivedDate", name="edit_kitting_short_received_date")
     */
    public function editKittingShortReceivedDate(Request $request, $shortId)
    {
        $receivedDate = $request->request->get('value');

        $short = $this->kittingShortRepository->findOneBy(array('id' => $shortId));
        if (!$short) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid kitting short"));
        }
        $short->setReceivedDate(new \DateTime($receivedDate));
        $short->setUpdateTime(new \DateTime());
        $short->setUpdatedBy($this->getUser());
        $this->em->persist($short);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/kittingShort/{shortId}/editEstimatedDeliveryDate", name="edit_kitting_short_estimated_delivery_date")
     */
    public function editKittingShortEstimatedDeliveryDate(Request $request, $shortId)
    {
        $estimateDeliveryDate = $request->request->get('value');

        $short = $this->kittingShortRepository->findOneBy(array('id' => $shortId));
        if (!$short) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid kitting short"));
        }
        $short->setEstimatedDeliveryDate(new \DateTime($estimatedDeliveryDate));
        $short->setUpdateTime(new \DateTime());
        $short->setUpdatedBy($this->getUser());
        $this->em->persist($short);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/kittingShort/{shortId}/editVendor", name="edit_kitting_short_vendor")
     */
    public function editKittingShortVendor(Request $request, $shortId)
    {
        $vendor = $request->request->get('value');

        $short = $this->kittingShortRepository->findOneBy(array('id' => $shortId));
        if (!$short) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid kitting short"));
        }
        $short->setVendor($vendor);
        $short->setUpdateTime(new \DateTime());
        $short->setUpdatedBy($this->getUser());
        $this->em->persist($short);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/kittingShort/{shortId}/editVendorPoNumber", name="edit_kitting_short_vendor_po_number")
     */
    public function editKittingShortVendorPoNumber(Request $request, $shortId)
    {
        $vendorPoNumber = $request->request->get('value');

        $short = $this->kittingShortRepository->findOneBy(array('id' => $shortId));
        if (!$short) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid kitting short"));
        }
        $short->setVendorPoNumber($vendorPoNumber);
        $short->setUpdateTime(new \DateTime());
        $short->setUpdatedBy($this->getUser());
        $this->em->persist($short);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/kittingShort/{shortId}/editModWo", name="edit_kitting_short_mod_wo")
     */
    public function editKittingShortModWo(Request $request, $shortId)
    {
        $modWo = $request->request->get('value');

        $short = $this->kittingShortRepository->findOneBy(array('id' => $shortId));
        if (!$short) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid kitting short"));
        }
        $short->setWoMod($modWo);
        $short->setUpdateTime(new \DateTime());
        $short->setUpdatedBy($this->getUser());
        $this->em->persist($short);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/kittingShort/{shortId}/editModDoneDate", name="edit_kitting_short_mod_done_date")
     */
    public function editKittingShortModDoneDate(Request $request, $shortId)
    {
        $modDoneDate = $request->request->get('value');

        $short = $this->kittingShortRepository->findOneBy(array('id' => $shortId));
        if (!$short) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid kitting short"));
        }
        $short->setModDoneDate(new \DateTime($modDoneDate));
        $short->setUpdateTime(new \DateTime());
        $short->setUpdatedBy($this->getUser());
        $this->em->persist($short);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/kittingShort/{shortId}/editNotes", name="edit_kitting_short_notes")
     */
    public function editKittingShortNotes(Request $request, $shortId)
    {
        $notes = $request->request->get('value');

        $short = $this->kittingShortRepository->findOneBy(array('id' => $shortId));
        if (!$short) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid kitting short"));
        }
        $short->setNotes($notes);
        $short->setUpdateTime(new \DateTime());
        $short->setUpdatedBy($this->getUser());
        $this->em->persist($short);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/scheduling/{jobId}/editSubReady", name="edit_scheduling_sub_ready")
     */
    public function editSchedulingSubReady(Request $request, $jobId)
    {
        $subReady = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }
        $scheduling = $this->schedulingRepository->findOneBy(array('job' => $job));
        if (!$scheduling) {
            $scheduling = new Shipping();
            $scheduling->setJob($job);
        }
        $scheduling->setSubReady($subReady);
        $scheduling->setUpdateTime(new \DateTime());
        $scheduling->setUpdatedBy($this->getUser());
        $this->em->persist($scheduling);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/scheduling/{jobId}/editCompletionDate", name="edit_scheduling_completion_date")
     */
    public function editSchedulingCompletionDate(Request $request, $jobId)
    {
        $completionDate = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }
        $scheduling = $this->schedulingRepository->findOneBy(array('job' => $job));
        if (!$scheduling) {
            $scheduling = new Shipping();
            $scheduling->setJob($job);
        }
        $scheduling->setCompletionDate(new \DateTime($completionDate));
        $scheduling->setUpdateTime(new \DateTime());
        $scheduling->setUpdatedBy($this->getUser());
        $this->em->persist($scheduling);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

}

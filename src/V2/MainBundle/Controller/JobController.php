<?php

namespace V2\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Doctrine\ORM\EntityManager;
use V2\MainBundle\Entity\Job;
use V2\MainBundle\Entity\Bom;
use V2\MainBundle\Entity\Paint;
use V2\MainBundle\Entity\Kitting;
use V2\MainBundle\Entity\KittingShort;
use V2\MainBundle\Entity\Shipping;
use V2\MainBundle\Entity\Scheduling;
use V2\MainBundle\Form\JobType;
use V2\MainBundle\Form\EditJobType;
use V2\MainBundle\Form\BomType;
use V2\MainBundle\Form\KittingType;
use V2\MainBundle\Form\KittingShortType;
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
     * @Route("/jobNames", name="job_names")
     */
    public function jobNames(Request $request)
    {
        $searchValue = $request->get('searchValue', '');

        $jobs = $this->jobRepository->findByJobName($searchValue);
        $jobNames = array();
        foreach ($jobs as $job) {
            if (!in_array($job->getName(), $jobNames)) {
                $jobNames[] = $job->getName();
            }
        }
        return new JsonResponse($jobNames);
    }

    /**
     * @Route("/everything", name="everything")
     */
    public function listEverything(Request $request)
    {
        $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        // Check for permissions
        if (!($isAdmin)) {
            $this->addFlash('error', 'You have no permission!');
            return $this->redirect($this->generateUrl('dashboard'));
        }

        $today = new \DateTime();
        $defaultParameters = array(
            'name'                  => null,
            'sales_order'           => null,
            'esd_date_from'         => null,
            'esd_date_to'           => null,
            'non_shipped'           => 1,
            'planner_esd_date_from' => null,
            'planner_esd_date_to'   => null,
            'planner_esd_week_from' => null,
            'planner_esd_week_to'   => null,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());

        $jobs = $this->jobRepository->findEverything($parameters);

        return $this->render('job/list_everything.html.twig', array(
            'jobs'                  =>  $jobs,
            'name'                  =>  $parameters['name'],
            'sales_order'           =>  $parameters['sales_order'],
            'esd_date_from'         =>  $parameters['esd_date_from'],
            'esd_date_to'           =>  $parameters['esd_date_to'],
            'non_shipped'           =>  $parameters['non_shipped'],
            'planner_esd_date_from' =>  $parameters['planner_esd_date_from'],
            'planner_esd_date_to'   =>  $parameters['planner_esd_date_to'],
            'planner_esd_week_from' =>  $parameters['planner_esd_week_from'],
            'planner_esd_week_to'   =>  $parameters['planner_esd_week_to'],
        ));
    }

    /**
     * @Route("/export/jobs", name="export_jobs")
     */
    public function exportJobs(Request $request)
    {
        $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        // Check for permissions
        if (!($isAdmin)) {
            $this->addFlash('error', 'You have no permission!');
            return $this->redirect($this->generateUrl('dashboard'));
        }
        $defaultParameters = array(
            'name'              => null,
            'sales_order'       => null,
            'esd'               => null,
            'non_shipped'       => 1,
            'planner_esd_date_from' => null,
            'planner_esd_date_to' => null,
            'planner_esd_week_from' => null,
            'planner_esd_week_to' => null,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());
        $jobs = $this->jobRepository->findEverything($parameters);

        // Convert to array before it can be encoded into CSV format
        $dataArray = array();
        foreach ($jobs as $job) {
            // JOB //
            $jobArray = array();
            $jobArray['name'] = $job->getName();
            $jobArray['sales_order'] = $job->getSalesOrder();
            $jobArray['estimated_ship_date'] = $job->getEstimatedShipDate() ? $job->getEstimatedShipDate()->format('Y-m-d') : null;
            $jobArray['type'] = $job->getType();
            $jobArray['model'] = $job->getModel();
            $jobArray['quantity'] = $job->getQuantity();
            $jobArray['manufacturing_order'] = $job->getManufacturingOrder();
            $jobArray['mac_purchase_order'] = $job->getMacPurchaseOrder();
            $jobArray['build_location'] = $job->getBuildLocation()->getName();
            $jobArray['planner_estimated_ship_date'] = $job->getPlannerEstimatedShipDate() ? $job->getPlannerEstimatedShipDate()->format('Y-m-d') : null;
            $jobArray['cancelled_date'] = $job->getCancelledDate() ? $job->getCancelledDate()->format('Y-m-d') : null;
            // BOM //
            $bomArray = array();
            $bomArray['serials_generated_date'] = $job->getBom() && $job->getBom()->getSerialsGeneratedDate() ? $job->getBom()->getSerialsGeneratedDate()->format('Y-m-d') : null;
            $bomArray['issued_date'] = $job->getBom() && $job->getBom()->getIssuedDate() ? $job->getBom()->getIssuedDate()->format('Y-m-d') : null;
            $bomArray['issued_by'] = $job->getBom() && $job->getBom()->getIssuedBy() ? $job->getBom()->getIssuedBy()->getInitials() : '';
            // SHIPPING // 
            $shippingArray = array();
            $shippingArray['is_complete'] = 'Empty';
            if ($job->getShipping() && $job->getShipping()->getIsComplete() == 1) {
                $shippingArray['is_complete'] = 'Partial';
            } elseif ($job->getShipping() && $job->getShipping()->getIsComplete() == 2) {
                $shippingArray['is_complete'] = 'Complete';
            }
            $shippingArray['ship_date'] = $job->getShipping() && $job->getShipping()->getShipDate() ? $job->getShipping()->getShipDate()->format('Y-m-d') : null;
            $shippingArray['second_ship_date'] = $job->getShipping() && $job->getShipping()->getSecondShipDate() ? $job->getShipping()->getSecondShipDate()->format('Y-m-d') : null;
            $shippingArray['notes'] = $job->getShipping() ? $job->getShipping()->getNotes() : '';

            // KITTING //
            $kittingArray = array();
            $kittingArray['kit_date'] = $job->getKitting() && $job->getKitting()->getKitDate() ? $job->getKitting()->getKitDate()->format('Y-m-d') : null;
            $kittingArray['completion_date'] = $job->getKitting() && $job->getKitting()->getCompletionDate() ? $job->getKitting()->getCompletionDate()->format('Y-m-d') : null;
            $kittingArray['filled_completely'] = 'Empty';
            if ($job->getKitting() && $job->getKitting()->getFilledCompletely() == 1) {
                $kittingArray['filled_completely'] = 'Yes';
            } elseif ($job->getKitting() && $job->getKitting()->getFilledCompletely() == 0) {
                $kittingArray['filled_completely'] = 'No';
            }
            $kittingArray['kit_location'] = $job->getKitting() ? $job->getKitting()->getLocation() : '';
            // SHORT 1 //
            $kittingArray['short1_part_number'] = $job->getKitting() && $job->getKitting()->getKittingShort1() ? $job->getKitting()->getKittingShort1()->getPartNumber() : null;
            $kittingArray['short1_short_class'] = "Empty";
            if ($job->getKitting() && $job->getKitting()->getKittingShort1() && $job->getKitting()->getKittingShort1()->getShortClass() == 1) {
                $kittingArray['short1_short_class'] = "Painted";
            } elseif ($job->getKitting() && $job->getKitting()->getKittingShort1() && $job->getKitting()->getKittingShort1()->getShortClass() == 2) {
                $kittingArray['short1_short_class'] = "Ignore";
            }
            $kittingArray['short1_date_needed'] = $job->getKitting() && $job->getKitting()->getKittingShort1() && $job->getKitting()->getKittingShort1()->getDateNeeded() ? $job->getKitting()->getKittingShort1()->getDateNeeded()->format('Y-m-d') : null;
            $kittingArray['short1_vendor'] = $job->getKitting() && $job->getKitting()->getKittingShort1() ? $job->getKitting()->getKittingShort1()->getVendor() : null;
            $kittingArray['short1_vendor_po_number'] = $job->getKitting() && $job->getKitting()->getKittingShort1() ? $job->getKitting()->getKittingShort1()->getVendorPoNumber() : null;
            $kittingArray['short1_mod_wo'] = $job->getKitting() && $job->getKitting()->getKittingShort1() ? $job->getKitting()->getKittingShort1()->getModWo() : null;
            $kittingArray['short1_estimated_delivery_date'] = $job->getKitting() && $job->getKitting()->getKittingShort1() && $job->getKitting()->getKittingShort1()->getEstimatedDeliveryDate() ? $job->getKitting()->getKittingShort1()->getEstimatedDeliveryDate()->format('Y-m-d') : null;
            $kittingArray['short1_mod_done_date'] = $job->getKitting() && $job->getKitting()->getKittingShort1() && $job->getKitting()->getKittingShort1()->getModDoneDate() ? $job->getKitting()->getKittingShort1()->getModDoneDate()->format('Y-m-d') : null;
            $kittingArray['short1_received_date'] = $job->getKitting() && $job->getKitting()->getKittingShort1() && $job->getKitting()->getKittingShort1()->getReceivedDate() ? $job->getKitting()->getKittingShort1()->getReceivedDate()->format('Y-m-d') : null;
            $kittingArray['short1_parts_pulled_date'] = $job->getKitting() && $job->getKitting()->getKittingShort1() && $job->getKitting()->getKittingShort1()->getPartsPulledDate() ? $job->getKitting()->getKittingShort1()->getPartsPulledDate()->format('Y-m-d') : null;
            $kittingArray['short1_notes'] = $job->getKitting() && $job->getKitting()->getKittingShort1() ? $job->getKitting()->getKittingShort1()->getNotes() : null;
            $kittingArray['short1_quantity'] = $job->getKitting() && $job->getKitting()->getKittingShort1() ? $job->getKitting()->getKittingShort1()->getQuantity() : null;

            // SHORT 2 //
            $kittingArray['short2_part_number'] = $job->getKitting() && $job->getKitting()->getKittingShort2() ? $job->getKitting()->getKittingShort1()->getPartNumber() : null;
            $kittingArray['short2_short_class'] = "Empty";
            if ($job->getKitting() && $job->getKitting()->getKittingShort2() && $job->getKitting()->getKittingShort2()->getShortClass() == 1) {
                $kittingArray['short2_short_class'] = "Painted";
            } elseif ($job->getKitting() && $job->getKitting()->getKittingShort2() && $job->getKitting()->getKittingShort2()->getShortClass() == 2) {
                $kittingArray['short2_short_class'] = "Ignore";
            }
            $kittingArray['short2_date_needed'] = $job->getKitting() && $job->getKitting()->getKittingShort2() && $job->getKitting()->getKittingShort2()->getDateNeeded() ? $job->getKitting()->getKittingShort2()->getDateNeeded()->format('Y-m-d') : null;
            $kittingArray['short2_vendor'] = $job->getKitting() && $job->getKitting()->getKittingShort2() ? $job->getKitting()->getKittingShort2()->getVendor() : null;
            $kittingArray['short2_vendor_po_number'] = $job->getKitting() && $job->getKitting()->getKittingShort2() ? $job->getKitting()->getKittingShort2()->getVendorPoNumber() : null;
            $kittingArray['short2_mod_wo'] = $job->getKitting() && $job->getKitting()->getKittingShort2() ? $job->getKitting()->getKittingShort2()->getModWo() : null;
            $kittingArray['short2_estimated_delivery_date'] = $job->getKitting() && $job->getKitting()->getKittingShort2() && $job->getKitting()->getKittingShort2()->getEstimatedDeliveryDate() ? $job->getKitting()->getKittingShort2()->getEstimatedDeliveryDate()->format('Y-m-d') : null;
            $kittingArray['short2_mod_done_date'] = $job->getKitting() && $job->getKitting()->getKittingShort2() && $job->getKitting()->getKittingShort2()->getModDoneDate() ? $job->getKitting()->getKittingShort2()->getModDoneDate()->format('Y-m-d') : null;
            $kittingArray['short2_received_date'] = $job->getKitting() && $job->getKitting()->getKittingShort2() && $job->getKitting()->getKittingShort2()->getReceivedDate() ? $job->getKitting()->getKittingShort2()->getReceivedDate()->format('Y-m-d') : null;
            $kittingArray['short2_parts_pulled_date'] = $job->getKitting() && $job->getKitting()->getKittingShort2() && $job->getKitting()->getKittingShort2()->getPartsPulledDate() ? $job->getKitting()->getKittingShort2()->getPartsPulledDate()->format('Y-m-d') : null;
            $kittingArray['short2_notes'] = $job->getKitting() && $job->getKitting()->getKittingShort2() ? $job->getKitting()->getKittingShort2()->getNotes() : null;
            $kittingArray['short2_quantity'] = $job->getKitting() && $job->getKitting()->getKittingShort2() ? $job->getKitting()->getKittingShort2()->getQuantity() : null;

            // SHORT 3 //
            $kittingArray['short3_part_number'] = $job->getKitting() && $job->getKitting()->getKittingShort3() ? $job->getKitting()->getKittingShort1()->getPartNumber() : null;
            $kittingArray['short3_short_class'] = "Empty";
            if ($job->getKitting() && $job->getKitting()->getKittingShort3() && $job->getKitting()->getKittingShort3()->getShortClass() == 1) {
                $kittingArray['short3_short_class'] = "Painted";
            } elseif ($job->getKitting() && $job->getKitting()->getKittingShort3() && $job->getKitting()->getKittingShort3()->getShortClass() == 2) {
                $kittingArray['short3_short_class'] = "Ignore";
            }
            $kittingArray['short3_date_needed'] = $job->getKitting() && $job->getKitting()->getKittingShort3() && $job->getKitting()->getKittingShort3()->getDateNeeded() ? $job->getKitting()->getKittingShort3()->getDateNeeded()->format('Y-m-d') : null;
            $kittingArray['short3_vendor'] = $job->getKitting() && $job->getKitting()->getKittingShort3() ? $job->getKitting()->getKittingShort3()->getVendor() : null;
            $kittingArray['short3_vendor_po_number'] = $job->getKitting() && $job->getKitting()->getKittingShort3() ? $job->getKitting()->getKittingShort3()->getVendorPoNumber() : null;
            $kittingArray['short3_mod_wo'] = $job->getKitting() && $job->getKitting()->getKittingShort3() ? $job->getKitting()->getKittingShort3()->getModWo() : null;
            $kittingArray['short3_estimated_delivery_date'] = $job->getKitting() && $job->getKitting()->getKittingShort3() && $job->getKitting()->getKittingShort3()->getEstimatedDeliveryDate() ? $job->getKitting()->getKittingShort3()->getEstimatedDeliveryDate()->format('Y-m-d') : null;
            $kittingArray['short3_mod_done_date'] = $job->getKitting() && $job->getKitting()->getKittingShort3() && $job->getKitting()->getKittingShort3()->getModDoneDate() ? $job->getKitting()->getKittingShort3()->getModDoneDate()->format('Y-m-d') : null;
            $kittingArray['short3_received_date'] = $job->getKitting() && $job->getKitting()->getKittingShort3() && $job->getKitting()->getKittingShort3()->getReceivedDate() ? $job->getKitting()->getKittingShort3()->getReceivedDate()->format('Y-m-d') : null;
            $kittingArray['short3_parts_pulled_date'] = $job->getKitting() && $job->getKitting()->getKittingShort3() && $job->getKitting()->getKittingShort3()->getPartsPulledDate() ? $job->getKitting()->getKittingShort3()->getPartsPulledDate()->format('Y-m-d') : null;
            $kittingArray['short3_notes'] = $job->getKitting() && $job->getKitting()->getKittingShort3() ? $job->getKitting()->getKittingShort3()->getNotes() : null;
            $kittingArray['short3_quantity'] = $job->getKitting() && $job->getKitting()->getKittingShort3() ? $job->getKitting()->getKittingShort3()->getQuantity() : null;

            // SHORT 4 //
            $kittingArray['short4_part_number'] = $job->getKitting() && $job->getKitting()->getKittingShort4() ? $job->getKitting()->getKittingShort1()->getPartNumber() : null;
            $kittingArray['short4_short_class'] = "Empty";
            if ($job->getKitting() && $job->getKitting()->getKittingShort4() && $job->getKitting()->getKittingShort4()->getShortClass() == 1) {
                $kittingArray['short4_short_class'] = "Painted";
            } elseif ($job->getKitting() && $job->getKitting()->getKittingShort4() && $job->getKitting()->getKittingShort4()->getShortClass() == 2) {
                $kittingArray['short4_short_class'] = "Ignore";
            }
            $kittingArray['short4_date_needed'] = $job->getKitting() && $job->getKitting()->getKittingShort4() && $job->getKitting()->getKittingShort4()->getDateNeeded() ? $job->getKitting()->getKittingShort4()->getDateNeeded()->format('Y-m-d') : null;
            $kittingArray['short4_vendor'] = $job->getKitting() && $job->getKitting()->getKittingShort4() ? $job->getKitting()->getKittingShort4()->getVendor() : null;
            $kittingArray['short4_vendor_po_number'] = $job->getKitting() && $job->getKitting()->getKittingShort4() ? $job->getKitting()->getKittingShort4()->getVendorPoNumber() : null;
            $kittingArray['short4_mod_wo'] = $job->getKitting() && $job->getKitting()->getKittingShort4() ? $job->getKitting()->getKittingShort4()->getModWo() : null;
            $kittingArray['short4_estimated_delivery_date'] = $job->getKitting() && $job->getKitting()->getKittingShort4() && $job->getKitting()->getKittingShort4()->getEstimatedDeliveryDate() ? $job->getKitting()->getKittingShort4()->getEstimatedDeliveryDate()->format('Y-m-d') : null;
            $kittingArray['short4_mod_done_date'] = $job->getKitting() && $job->getKitting()->getKittingShort4() && $job->getKitting()->getKittingShort4()->getModDoneDate() ? $job->getKitting()->getKittingShort4()->getModDoneDate()->format('Y-m-d') : null;
            $kittingArray['short4_received_date'] = $job->getKitting() && $job->getKitting()->getKittingShort4() && $job->getKitting()->getKittingShort4()->getReceivedDate() ? $job->getKitting()->getKittingShort4()->getReceivedDate()->format('Y-m-d') : null;
            $kittingArray['short4_parts_pulled_date'] = $job->getKitting() && $job->getKitting()->getKittingShort4() && $job->getKitting()->getKittingShort4()->getPartsPulledDate() ? $job->getKitting()->getKittingShort4()->getPartsPulledDate()->format('Y-m-d') : null;
            $kittingArray['short4_notes'] = $job->getKitting() && $job->getKitting()->getKittingShort4() ? $job->getKitting()->getKittingShort4()->getNotes() : null;
            $kittingArray['short4_quantity'] = $job->getKitting() && $job->getKitting()->getKittingShort4() ? $job->getKitting()->getKittingShort4()->getQuantity() : null;

            // SCHEDULING //
            $schedulingArray = array();
            $schedulingArray['priority'] = 'Normal';
            if ($job->getScheduling() && $job->getScheduling()->getPriority() == -1) {
                $schedulingArray['priority'] = 'Extra';
            } elseif ($job->getScheduling() && $job->getScheduling()->getPriority() == 1) {
                $schedulingArray['priority'] = 'Custom';
            } elseif ($job->getScheduling() && $job->getScheduling()->getPriority() == 2) {
                $schedulingArray['priority'] = 'Hot';
            } elseif ($job->getScheduling() && $job->getScheduling()->getPriority() == 3) {
                $schedulingArray['priority'] = 'Rush';
            } elseif ($job->getScheduling() && $job->getScheduling()->getPriority() == 4) {
                $schedulingArray['priority'] = 'RMA';
            }
            $schedulingArray['sub_ready'] = $job->getScheduling() && $job->getScheduling()->getSubReady() == 1 ? 'Yes' : 'No';
            $schedulingArray['completion_date'] = $job->getScheduling() && $job->getScheduling()->getCompletionDate() ? $job->getScheduling()->getCompletionDate()->format('Y-m-d') : null;
            $schedulingArray['built_by'] = $job->getScheduling() ? $job->getScheduling()->getBuiltBy() : ''; // already in Initials

            // PAINT //
            $paintArray = array();
            $paintArray['color1'] = $job->getPaint() ? $job->getPaint()->getColor1() : '';
            $paintArray['batch1'] = $job->getPaint() && $job->getPaint()->getBatch1() ? $job->getPaint()->getBatch1()->getId() : '';
            $paintArray['color2'] = $job->getPaint() ? $job->getPaint()->getColor2() : '';
            $paintArray['batch2'] = $job->getPaint() && $job->getPaint()->getBatch2() ? $job->getPaint()->getBatch2()->getId() : '';
            $paintArray['paint_location'] = $job->getPaint() ? $job->getPaint()->getLocation() : '';

            $dataArray[] = array_merge($jobArray, $bomArray, $shippingArray, $kittingArray, $schedulingArray, $paintArray);
        }

        $serializer = $this->get('serializer');
        $csvData = $serializer->encode($dataArray, 'csv');

        // Downloads the file to the user's computer
        $response = new Response();
        $response->headers->set('Content-type', 'text/csv');
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-Disposition', 'attachment; filename=jobs.csv;');
        $response->sendHeaders();
        $response->setContent($csvData);
        return $response;
    }

    /**
     * @Route("/all", name="all_jobs")
     */
    public function listAllJobs(Request $request)
    {
        $defaultParameters = array(
            'see_all' => 0,
            'name' => null,
            'sales_order' => null,
            'planner_esd' => null,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());

        $jobs           = $this->jobRepository->findAllJobs($parameters);

        return $this->render('job/list_all.html.twig', array(
            'jobs'          =>  $jobs,
            'see_all'       =>  $parameters['see_all'],
            'name'          =>  $parameters['name'],
            'sales_order'   =>  $parameters['sales_order'],
            'planner_esd'   =>  $parameters['planner_esd'],
        ));
    }

    /**
     * @Route("/bomBuilder", name="bom_builder")
     */
    public function listBomBuilderJobs(Request $request)
    {
        $defaultParameters = array(
            'name' => null,
            'sales_order' => null,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());

        $jobs           = $this->jobRepository->findBomBuilderJobs($parameters);

        return $this->render('job/list_bom_builder.html.twig', array(
            'jobs'          =>  $jobs,
            'name'          =>  $parameters['name'],
            'sales_order'   =>  $parameters['sales_order'],
        ));
    }

    /**
     * @Route("/kitter", name="kitter")
     */
    public function listKitterJobs(Request $request)
    {
        $defaultParameters = array(
            'name' => null,
            'sales_order' => null,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());

        $jobs           = $this->jobRepository->findKitterJobs($parameters);

        return $this->render('job/list_kitter.html.twig', array(
            'jobs'          =>  $jobs,
            'name'          =>  $parameters['name'],
            'sales_order'   =>  $parameters['sales_order'],
        ));
    }

    /**
     * @Route("/short_kits", name="short_kits")
     */
    public function listShortKitsJobs(Request $request)
    {
        $defaultParameters = array(
            'name' => null,
            'sales_order' => null,
            'part_number' => null,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());

        $jobs           = $this->jobRepository->findShortKitsJobs($parameters);

        return $this->render('job/list_short_kits.html.twig', array(
            'jobs'          =>  $jobs,
            'name'          =>  $parameters['name'],
            'sales_order'   =>  $parameters['sales_order'],
            'part_number'   =>  $parameters['part_number'],
        ));
    }

    /**
     * @Route("/receiver", name="receiver")
     */
    public function listReceiverParts(Request $request)
    {
        $defaultParameters = array(
            'part_number' => null,
            'vendor_po_number' => null,
            'completed' => false,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());

        $parts = $this->kittingShortRepository->findReceiverParts($parameters);

        return $this->render('job/list_receiver.html.twig', array(
            'parts'             =>  $parts,
            'part_number'       =>  $parameters['part_number'],
            'vendor_po_number'  =>  $parameters['vendor_po_number'],
            'completed'         =>  $parameters['completed'],
        ));
    }

    /**
     * @Route("/manufacturer", name="manufacturer")
     */
    public function listManufacturerParts(Request $request)
    {
        $defaultParameters = array(
            'date_needed_from' => null,
            'date_needed_to' => null,
            'part_number' => null,
            'sales_order' => null,
            'completed' => false,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());

        $parts = $this->kittingShortRepository->findManufacturerParts($parameters);

        return $this->render('job/list_manufacturer.html.twig', array(
            'parts'             =>  $parts,
            'date_needed_from'  =>  $parameters['date_needed_from'],
            'date_needed_to'    =>  $parameters['date_needed_to'],
            'part_number'       =>  $parameters['part_number'],
            'sales_order'       =>  $parameters['sales_order'],
            'completed'         =>  $parameters['completed'],
        ));
    }

    /**
     * @Route("/manufacturer/print", name="print_manufacturer")
     */
    public function printManufacturerParts(Request $request)
    {
        $defaultParameters = array(
            'date_needed_from' => null,
            'date_needed_to' => null,
            'part_number' => null,
            'sales_order' => null,
            'completed' => false,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());

        $parts = $this->kittingShortRepository->findManufacturerParts($parameters);

        return $this->render('job/print_manufacturer.html.twig', array(
            'parts'             =>  $parts,
            'date_needed_from'  =>  $parameters['date_needed_from'],
            'date_needed_to'    =>  $parameters['date_needed_to'],
            'part_number'       =>  $parameters['part_number'],
            'sales_order'       =>  $parameters['sales_order'],
            'completed'         =>  $parameters['completed'],
        ));
    }

    /**
     * @Route("/supplyChain", name="supply_chain")
     */
    public function listSupplyChainParts(Request $request)
    {
        $defaultParameters = array(
            'part_number' => null,
            'vendor' => null,
            'name' => null,
            'completed' => false,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());

        $parts = $this->kittingShortRepository->findSupplyChainParts($parameters);

        return $this->render('job/list_supply_chain.html.twig', array(
            'parts'         =>  $parts,
            'part_number'   =>  $parameters['part_number'],
            'vendor'        =>  $parameters['vendor'],
            'name'          =>  $parameters['name'],
            'completed'     =>  $parameters['completed'],
        ));
    }

    /**
     * @Route("/export/supplyChain", name="export_supply_chain")
     */
    public function exportSupplyChain(Request $request)
    {
        $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        // Check for permissions
        if (!($isAdmin)) {
            $this->addFlash('error', 'You have no permission!');
            return $this->redirect($this->generateUrl('dashboard'));
        }
        $defaultParameters = array(
            'part_number' => null,
            'vendor' => null,
            'name' => null,
            'completed' => false,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());
        $parts = $this->kittingShortRepository->findSupplyChainParts($parameters);

        // Convert to array before it can be encoded into CSV format
        $dataArray = array();
        foreach ($parts as $part) {
            $partArray = array();
            $partArray['part_number'] = $part->getPartNumber();
            $partArray['date_needed'] = $part->getDateNeeded() ? $part->getDateNeeded()->format('Y-m-d') : null;
            $partArray['estimated_delivery_date'] = $part->getEstimatedDeliveryDate() ? $part->getEstimatedDeliveryDate()->format('Y-m-d') : null;
            $partArray['vendor'] = $part->getVendor();
            $partArray['vendor_po'] = $part->getVendorPoNumber();
            $partArray['mod_wo'] = $part->getModWo();
            $partArray['received_date'] = $part->getReceivedDate();
            $partArray['notes'] = $part->getNotes();
            $partArray['job_name'] = $part->getKitting() ? $part->getKitting()->getJob()->getName() : null;
            $partArray['job_so_number'] = $part->getKitting() ? $part->getKitting()->getJob()->getSalesOrder() : null;
            $partArray['quantity'] = $part->getKitting() ? $part->getKitting()->getJob()->getQuantity() : null;
            $dataArray[] = $partArray;
        }
        $serializer = $this->get('serializer');
        $csvData = $serializer->encode($dataArray, 'csv');

        // Downloads the file to the user's computer
        $response = new Response();
        $response->headers->set('Content-type', 'text/csv');
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-Disposition', 'attachment; filename=supply_chain.csv;');
        $response->sendHeaders();
        $response->setContent($csvData);
        return $response;
    }

    /**
     * @Route("/macProduction", name="mac_production")
     */
    public function listMacProductionJobs(Request $request)
    {
        $defaultParameters = array(
            'sales_order' => null,
            'planner_esd_date_from' => null,
            'planner_esd_date_to' => null,
            'planner_esd_week_from' => null,
            'planner_esd_week_to' => null,
            'ctb' => null,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());

        $jobs           = $this->jobRepository->findMacProductionJobs($parameters);

        return $this->render('job/list_mac_production.html.twig', array(
            'jobs'                  =>  $jobs,
            'sales_order'           =>  $parameters['sales_order'],
            'planner_esd_date_from' =>  $parameters['planner_esd_date_from'],
            'planner_esd_date_to'   =>  $parameters['planner_esd_date_to'],
            'planner_esd_week_from' =>  $parameters['planner_esd_week_from'],
            'planner_esd_week_to'   =>  $parameters['planner_esd_week_to'],
            'ctb'                   =>  $parameters['ctb'],
        ));
    }

    /**
     * @Route("/macProduction/print", name="print_mac_production")
     */
    public function printMacProductionJobs(Request $request)
    {
        $defaultParameters = array(
            'sales_order' => null,
            'planner_esd_date_from' => null,
            'planner_esd_date_to' => null,
            'planner_esd_week_from' => null,
            'planner_esd_week_to' => null,
            'ctb' => null,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());

        $jobs           = $this->jobRepository->findMacProductionJobs($parameters);

        return $this->render('job/print_mac_production.html.twig', array(
            'jobs'      =>  $jobs,
            'sales_order'   =>  $parameters['sales_order'],
            'planner_esd_date_from' =>  $parameters['planner_esd_date_from'],
            'planner_esd_date_to'   =>  $parameters['planner_esd_date_to'],
            'planner_esd_week_from' =>  $parameters['planner_esd_week_from'],
            'planner_esd_week_to'   =>  $parameters['planner_esd_week_to'],
            'ctb'                   =>  $parameters['ctb'],
        ));
    }

    /**
     * @Route("/v2Production", name="v2_production")
     */
    public function listV2ProductionJobs(Request $request)
    {
        $defaultParameters = array(
            'sales_order' => null,
            'planner_esd_date_from' => null,
            'planner_esd_date_to' => null,
            'planner_esd_week_from' => null,
            'planner_esd_week_to' => null,
            'ctb' => null,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());

        $jobs           = $this->jobRepository->findV2ProductionJobs($parameters);

        return $this->render('job/list_v2_production.html.twig', array(
            'jobs'                  =>  $jobs,
            'sales_order'           =>  $parameters['sales_order'],
            'planner_esd_date_from' =>  $parameters['planner_esd_date_from'],
            'planner_esd_date_to'   =>  $parameters['planner_esd_date_to'],
            'planner_esd_week_from' =>  $parameters['planner_esd_week_from'],
            'planner_esd_week_to'   =>  $parameters['planner_esd_week_to'],
            'ctb'                   =>  $parameters['ctb'],
        ));
    }

    /**
     * @Route("/v2Production/print", name="print_v2_production")
     */
    public function printV2ProductionJobs(Request $request)
    {
        $defaultParameters = array(
            'sales_order' => null,
            'planner_esd_date_from' => null,
            'planner_esd_date_to' => null,
            'planner_esd_week_from' => null,
            'planner_esd_week_to' => null,
            'ctb' => null,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());

        $jobs           = $this->jobRepository->findV2ProductionJobs($parameters);

        return $this->render('job/print_v2_production.html.twig', array(
            'jobs'                  =>  $jobs,
            'sales_order'           =>  $parameters['sales_order'],
            'planner_esd_date_from' =>  $parameters['planner_esd_date_from'],
            'planner_esd_date_to'   =>  $parameters['planner_esd_date_to'],
            'planner_esd_week_from' =>  $parameters['planner_esd_week_from'],
            'planner_esd_week_to'   =>  $parameters['planner_esd_week_to'],
            'ctb'                   =>  $parameters['ctb'],
        ));
    }

    /**
     * @Route("/shipper", name="shipper")
     */
    public function listShipperJobs(Request $request)
    {
        $defaultParameters = array(
            'sales_order' => null,
            'ship_date' => null,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());

        $jobs           = $this->jobRepository->findShipperJobs($parameters);

        return $this->render('job/list_shipper.html.twig', array(
            'jobs'          =>  $jobs,
            'sales_order'   =>  $parameters['sales_order'],
            'ship_date'     =>  $parameters['ship_date'],
        ));
    }
    /**
     * @Route("/scheduler", name="scheduler")
     */
    public function listSchedulerJobs(Request $request)
    {
        // Grab values from session data (if any)
        $session = $request->getSession();
        $selectedFilterParameter = $session->get('scheduler_parameter_selected_filter');
        $selectedLocationParameter = $session->get('scheduler_parameter_selected_location');
        $selectedPriorityParameter = $session->get('scheduler_parameter_selected_priority');

        $defaultParameters = array(
            'name'                  => null,
            'sales_order'           => null,
            'filled_completely'     => null,
            'non_shipped'           => 1,
            'selected_filter'       => $selectedFilterParameter != null ? $selectedFilterParameter : 0, // means Not Vetted
            'selected_location'     => $selectedLocationParameter != null ? $selectedLocationParameter : 0, // means ALL locations
            'selected_priority'     => $selectedPriorityParameter != null ? $selectedPriorityParameter : 99, // means ALL
            'esd_date_from'         => null,
            'esd_date_to'           => null,
            'planner_esd_week_from' => null,
            'planner_esd_week_to'   => null,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());
        // Save search settings
        $session->set('scheduler_parameter_selected_filter', $parameters['selected_filter']);
        $session->set('scheduler_parameter_selected_location', $parameters['selected_location']);
        $session->set('scheduler_parameter_selected_priority', $parameters['selected_priority']);
        // Check if the filter is set to Not Vetted, we automatically set the priority to follow the same
        if ($parameters['selected_filter'] == 0) {
            $parameters['selected_priority'] = -2; // means Not vetter
        }
        $jobs = $this->jobRepository->findSchedulerJobs($parameters);
        $locations = $this->em->getRepository('V2MainBundle:BuildLocation')->findAll();

        return $this->render('job/list_scheduler.html.twig', array(
            'jobs'                  =>  $jobs,
            'locations'             =>  $locations,
            'name'                  =>  $parameters['name'],
            'sales_order'           =>  $parameters['sales_order'],
            'filled_completely'     =>  $parameters['filled_completely'],
            'non_shipped'           =>  $parameters['non_shipped'],
            'selected_filter'       =>  $parameters['selected_filter'],
            'selected_location'     =>  $parameters['selected_location'],
            'selected_priority'     =>  $parameters['selected_priority'],
            'esd_date_from'         =>  $parameters['esd_date_from'],
            'esd_date_to'           =>  $parameters['esd_date_to'],
            'planner_esd_week_from' =>  $parameters['planner_esd_week_from'],
            'planner_esd_week_to'   =>  $parameters['planner_esd_week_to'],
        ));
    }

    /**
     * @Route("/create-job", name="create_job")
     */
    public function createJob(Request $request)
    {
        $job = new Job();
        $form = $this->createForm(JobType::class, $job);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $job->setEstimatedShipDate(new \DateTime($form->get("estimatedShipDate")->getData()));
                    $job->setPlannerEstimatedShipDate(new \DateTime($form->get("plannerEstimatedShipDate")->getData()));
                    $job->setCreatedBy($this->getUser());
                    $job->setCreateTime(new \DateTime());
                    $job->setUpdatedBy($this->getUser());
                    $job->setUpdateTime(new \DateTime());
                    $this->em->persist($job);
                    $this->em->flush();

                    $paint = new Paint();
                    $paint->setJob($job);
                    $paint->setColor1($form->get("paint1")->getData());
                    $paint->setColor2($form->get("paint2")->getData());
                    $paint->setUpdatedBy($this->getUser());
                    $paint->setUpdateTime(new \DateTime());
                    $this->em->persist($paint);

                    $bom = new Bom();
                    $bom->setJob($job);
                    $bom->setUpdatedBy($this->getUser());
                    $bom->setUpdateTime(new \DateTime());
                    $this->em->persist($bom);

                    $kitting = new Kitting();
                    $kitting->setJob($job);
                    $kitting->setCreatedBy($this->getUser());
                    $kitting->setUpdatedBy($this->getUser());
                    $kitting->setUpdateTime(new \DateTime());
                    $this->em->persist($kitting);

                    $scheduling = new Scheduling();
                    $scheduling->setJob($job);
                    $scheduling->setPriority($form->get("priority")->getData());
                    $scheduling->setUpdatedBy($this->getUser());
                    $scheduling->setUpdateTime(new \DateTime());
                    $this->em->persist($scheduling);

                    $this->em->flush();
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error while updating! '.$e->getMessage());
                    return $this->redirect($request->getUri());
                }

                $this->addFlash('success', 'Job created!');
                if ($form->get('createAnotherCopy')->isClicked()) {
                    // Copy the NAME, SO#, ESD, LOC fields since it's a similar job
                    $job = new Job();
                    $job->setName($form->get("name")->getData());
                    $job->setSalesOrder($form->get("salesOrder")->getData());
                    $job->setEstimatedShipDate($form->get("estimatedShipDate")->getData());
                    $job->setBuildLocation($form->get("buildLocation")->getData());
                    $job->setPlannerEstimatedShipDate($form->get("plannerEstimatedShipDate")->getData());
                    $form = $this->createForm(JobType::class, $job);

                    return $this->render('job/create.html.twig', array(
                        'job'       =>  $job,
                        'form'      =>  $form->createView(),
                    ));
                } else {
                    return $this->redirect($this->generateUrl('dashboard'));
                }
            }
        }

        return $this->render('job/create.html.twig', array(
            'job'       =>  $job,
            'form'      =>  $form->createView(),
        ));

    }

    /**
     * @Route("/create-part", name="create_part")
     */
    public function createPart(Request $request)
    {
        $part = new KittingShort();
        $form = $this->createForm(KittingShortType::class, $part);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $part->setUpdatedBy($this->getUser());
                    $part->setUpdateTime(new \DateTime());
                    $this->em->persist($part);
                    $this->em->flush();
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error while creating new Part! '.$e->getMessage());
                    return $this->redirect($request->getUri());
                }

                $this->addFlash('success', 'Part created!');
                return $this->redirect($this->generateUrl('supply_chain'));
            }
        }

        return $this->render('part/create.html.twig', array(
            'part'      =>  $part,
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

        if (is_null($estimatedShipDate) || $estimatedShipDate == '') {
            $job->setEstimatedShipDate(null);
        } else {
            $job->setEstimatedShipDate(new \DateTime($estimatedShipDate));
        }
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

        $bom = $this->bomRepository->findOneBy(array('job' => $job));
        if (!$bom) {
            $bom = new Bom();
            $bom->setJob($job);
        }

        $bom->setIssuedDate(new \DateTime());
        $bom->setIssuedBy($this->getUser());
        $this->em->persist($bom);

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
     * @Route("/job/{jobId}/editNotes", name="edit_job_notes")
     */
    public function editJobNotes(Request $request, $jobId)
    {
        $notes = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }

        $job->setNotes($notes);
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

        if (is_null($plannerEstimatedShipDate) || $plannerEstimatedShipDate == '') {
            $job->setPlannerEstimatedShipDate(null);
        } else {
            $job->setPlannerEstimatedShipDate(new \DateTime($plannerEstimatedShipDate));
        }
        $job->setUpdateTime(new \DateTime());
        $job->setUpdatedBy($this->getUser());
        $this->em->persist($job);
        $this->em->flush();

        return $this->json(array('status' => 'success', 'weekNum' => date('W', strtotime($plannerEstimatedShipDate))));
    }

    /**
     * @Route("/job/{jobId}/delete", name="delete_job")
     */
    public function deleteJob(Request $request, $jobId)
    {
        $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        // Check for permissions
        if (!($isAdmin)) {
            $this->addFlash('error', 'You have no permission!');
            return $this->redirect($this->generateUrl('dashboard'));
        }

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            $this->addFlash('error', 'Invalid job');
            return $this->redirect($this->generateUrl('everything'));
        }

        $job->setCancelledDate(new \DateTime());
        $job->setUpdateTime(new \DateTime());
        $job->setUpdatedBy($this->getUser());
        $this->em->persist($job);
        $this->em->flush();

        $this->addFlash('success', 'Job deleted');
        return $this->redirect($this->generateUrl('everything'));
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

        if(is_null($serialsGeneratedDate) || $serialsGeneratedDate == '') {
            $bom->setSerialsGeneratedDate(null);
        } else {
            $bom->setSerialsGeneratedDate(new \DateTime($serialsGeneratedDate));
        }
        $this->em->persist($bom);
        $this->em->flush();

        return $this->json(array('status' => 'success', 'msg'=>$serialsGeneratedDate));
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

        if (is_null($issuedDate) || $issuedDate == '') {
            $bom->setIssuedDate(null);
        } else {
            $bom->setIssuedDate(new \DateTime($issuedDate));
        }
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

        if (is_null($secondShipDate) || $secondShipDate == '') {
            $shipping->setSecondShipDate(null);
        } else {       
            $shipping->setSecondShipDate(new \DateTime($secondShipDate));
        }
        $shipping->setUpdateTime(new \DateTime());
        $shipping->setUpdatedBy($this->getUser());
        $this->em->persist($shipping);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/shipping/{jobId}/editNotes", name="edit_shipping_notes")
     */
    public function editShippingNotes(Request $request, $jobId)
    {
        $notes = $request->request->get('value');
        
        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }
        $shipping = $this->shippingRepository->findOneBy(array('job' => $job));
        if (!$shipping) {
            $shipping = new Shipping();
            $shipping->setJob($job);
        }
        $shipping->setNotes($notes);
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

        $kitting->setFilledCompletely(($filledCompletely == 'Empty') ? null : $filledCompletely );
        // As long as there's a kitted date; they no longer care if it's completely done (Completion date)
        $kitting->setKitDate(new \DateTime());
        $kitting->setCompletionDate(new \DateTime()); // completion date and kit date are the same now
        $kitting->setUpdateTime(new \DateTime());
        $kitting->setUpdatedBy($this->getUser());

        $this->em->persist($kitting);
        $this->em->flush();
        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/kitting/{jobId}/editKitDate", name="edit_kitting_kit_date")
     */
    public function editKittingKitDate(Request $request, $jobId)
    {
        $kitDate = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        $kitting = $this->kittingRepository->findOneBy(array('job' => $job));
        if (!$kitting) {
            $kitting = new Kitting();
            $kitting->setJob($job);
        }

        if (is_null($kitDate) || $kitDate == '') {
            $kitting->setKitDate(null);
        } else {
            $kitting->setKitDate(new \DateTime($kitDate));
        }
        $kitting->setCompletionDate(new \DateTime($kitDate)); // completion date and kit date are the same now
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

        if (is_null($completionDate) || $completionDate == '') {
            $kitting->setCompletionDate(null);
        } else {
            $kitting->setCompletionDate(new \DateTime($completionDate));
        }
        $kitting->setUpdateTime(new \DateTime());
        $kitting->setUpdatedBy($this->getUser());

        $this->em->persist($kitting);
        $this->em->flush();
        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/kittingShort/{shortId}/editShortClass", name="edit_kitting_short_short_class")
     */
    public function editKittingShortShortClass(Request $request, $shortId)
    {
        $shortClass = $request->request->get('value');

        $short = $this->kittingShortRepository->findOneBy(array('id' => $shortId));
        if (!$short) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid kitting short"));
        }
        $short->setShortClass($shortClass);
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

        if (is_null($dateNeeded) || $dateNeeded == '') {
            $short->setDateNeeded(null);
        } else {
            $short->setDateNeeded(new \DateTime($dateNeeded));
        }
        $short->setUpdateTime(new \DateTime());
        $short->setUpdatedBy($this->getUser());
        $this->em->persist($short);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/kittingShort/{shortId}/editPartsPulledDate", name="edit_kitting_short_parts_pulledDate")
     */
    public function editKittingShortPartsPulledDate(Request $request, $shortId)
    {
        $partsPulledDate = $request->request->get('value');

        $short = $this->kittingShortRepository->findOneBy(array('id' => $shortId));
        if (!$short) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid kitting short"));
        }

        if (is_null($partsPulledDate) || $partsPulledDate == '') {
            $short->setPartsPulledDate(null);
        } else {
            $short->setPartsPulledDate(new \DateTime($partsPulledDate));
        }
        $short->setUpdateTime(new \DateTime());
        $short->setUpdatedBy($this->getUser());
        $this->em->persist($short);
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
        return $this->json(array('status' => 'success', 'shortId' => $short1->getId()));
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
        $short2->setUpdatedBy($this->getUser());
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
        return $this->json(array('status' => 'success', 'shortId' => $short2->getId()));
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
        $short3->setUpdatedBy($this->getUser());
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
        return $this->json(array('status' => 'success', 'shortId' => $short3->getId()));
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
        $short4->setUpdatedBy($this->getUser());
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
        return $this->json(array('status' => 'success', 'shortId' => $short4->getId()));
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

        if (is_null($receivedDate) || $receivedDate == '') {
            $short->setReceivedDate(null);
        } else {
            $short->setReceivedDate(new \DateTime($receivedDate));
        }
        $short->setUpdateTime(new \DateTime());
        $short->setUpdatedBy($this->getUser());
        $this->em->persist($short);

        // Also check if all shorts have been received so system will automatically set 'filled_completely' to true
        $kitting = $short->getKitting();
        if ($kitting && $kitting->receivedAllShorts()) {
            $kitting->setFilledCompletely(true);
            $kitting->setCompletionDate(new \DateTime());
            $this->em->persist($kitting);
        }

        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/kittingShort/{shortId}/editEstimatedDeliveryDate", name="edit_kitting_short_estimated_delivery_date")
     */
    public function editKittingShortEstimatedDeliveryDate(Request $request, $shortId)
    {
        $estimatedDeliveryDate = $request->request->get('value');

        $short = $this->kittingShortRepository->findOneBy(array('id' => $shortId));
        if (!$short) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid kitting short"));
        }

        if (is_null($estimatedDeliveryDate) || $estimatedDeliveryDate == '') {
            $short->setEstimatedDeliveryDate(null);
        } else {
            $short->setEstimatedDeliveryDate(new \DateTime($estimatedDeliveryDate));
        }
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
        $short->setModWo($modWo);
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

        if (is_null($modDoneDate) || $modDoneDate == '') {
            $short->setModDoneDate(null);
        } else {
            $short->setModDoneDate(new \DateTime($modDoneDate));
        }
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

        if (is_null($completionDate) || $completionDate == '') {
            $scheduling->setCompletionDate(null);
        } else {
            $scheduling->setCompletionDate(new \DateTime($completionDate));
        }
        $scheduling->setUpdateTime(new \DateTime());
        $scheduling->setUpdatedBy($this->getUser());
        $this->em->persist($scheduling);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/scheduling/{jobId}/editPriority", name="edit_scheduling_priority")
     */
    public function editSchedulingPriority(Request $request, $jobId)
    {
        $priority = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }
        $scheduling = $this->schedulingRepository->findOneBy(array('job' => $job));
        if (!$scheduling) {
            $scheduling = new Shipping();
            $scheduling->setJob($job);
        }
        $scheduling->setPriority($priority);
        $scheduling->setUpdateTime(new \DateTime());
        $scheduling->setUpdatedBy($this->getUser());
        $this->em->persist($scheduling);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/scheduling/{jobId}/editPriorityBomBuilder", name="edit_scheduling_priority_bom_builder")
     */
    public function editSchedulingPriorityBomBuilder(Request $request, $jobId)
    {
        $priorityBomBuilder = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }
        $scheduling = $this->schedulingRepository->findOneBy(array('job' => $job));
        if (!$scheduling) {
            $scheduling = new Shipping();
            $scheduling->setJob($job);
        }
        $scheduling->setPriorityBomBuilder($priorityBomBuilder);
        $scheduling->setUpdateTime(new \DateTime());
        $scheduling->setUpdatedBy($this->getUser());
        $this->em->persist($scheduling);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/scheduling/{jobId}/editPriorityKitter", name="edit_scheduling_priority_kitter")
     */
    public function editSchedulingPriorityKitter(Request $request, $jobId)
    {
        $priorityKitter = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }
        $scheduling = $this->schedulingRepository->findOneBy(array('job' => $job));
        if (!$scheduling) {
            $scheduling = new Shipping();
            $scheduling->setJob($job);
        }
        $scheduling->setPriorityKitter($priorityKitter);
        $scheduling->setUpdateTime(new \DateTime());
        $scheduling->setUpdatedBy($this->getUser());
        $this->em->persist($scheduling);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/scheduling/{jobId}/editPriorityMacProduction", name="edit_scheduling_priority_mac_production")
     */
    public function editSchedulingPriorityMacProduction(Request $request, $jobId)
    {
        $priorityMacProduction = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }
        $scheduling = $this->schedulingRepository->findOneBy(array('job' => $job));
        if (!$scheduling) {
            $scheduling = new Shipping();
            $scheduling->setJob($job);
        }
        $scheduling->setPriorityMacProduction($priorityMacProduction);
        $scheduling->setUpdateTime(new \DateTime());
        $scheduling->setUpdatedBy($this->getUser());
        $this->em->persist($scheduling);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/scheduling/{jobId}/editPriorityV2Production", name="edit_scheduling_priority_v2_production")
     */
    public function editSchedulingPriorityV2Production(Request $request, $jobId)
    {
        $priorityV2Production = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }
        $scheduling = $this->schedulingRepository->findOneBy(array('job' => $job));
        if (!$scheduling) {
            $scheduling = new Shipping();
            $scheduling->setJob($job);
        }
        $scheduling->setPriorityV2Production($priorityV2Production);
        $scheduling->setUpdateTime(new \DateTime());
        $scheduling->setUpdatedBy($this->getUser());
        $this->em->persist($scheduling);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/scheduling/{jobId}/editPriorityShipper", name="edit_scheduling_priority_shipper")
     */
    public function editSchedulingPriorityShipper(Request $request, $jobId)
    {
        $priorityShipper = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }
        $scheduling = $this->schedulingRepository->findOneBy(array('job' => $job));
        if (!$scheduling) {
            $scheduling = new Shipping();
            $scheduling->setJob($job);
        }
        $scheduling->setPriorityShipper($priorityShipper);
        $scheduling->setUpdateTime(new \DateTime());
        $scheduling->setUpdatedBy($this->getUser());
        $this->em->persist($scheduling);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/scheduling/{jobId}/editBuiltBy", name="edit_scheduling_built_by")
     */
    public function editSchedulingBuiltBy(Request $request, $jobId)
    {
        $builtBy = $request->request->get('value');

        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid job"));
        }
        $scheduling = $this->schedulingRepository->findOneBy(array('job' => $job));
        if (!$scheduling) {
            $scheduling = new Shipping();
            $scheduling->setJob($job);
        }
        $scheduling->setBuiltBy($builtBy);
        $scheduling->setUpdateTime(new \DateTime());
        $scheduling->setUpdatedBy($this->getUser());
        $this->em->persist($scheduling);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

}

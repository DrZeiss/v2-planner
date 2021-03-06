<?php

namespace V2\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Doctrine\ORM\EntityManager;
use V2\MainBundle\Entity\Job;
use V2\MainBundle\Entity\Paint;
use V2\MainBundle\Entity\Batch;

/**
 * @Route("/paint")
 */
class PaintController extends Controller
{
    protected $em;
    protected $jobRepository;
    protected $paintRepository;
    protected $batchRepository;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->jobRepository = $this->em->getRepository('V2MainBundle:Job');
        $this->paintRepository = $this->em->getRepository('V2MainBundle:Paint');
        $this->batchRepository = $this->em->getRepository('V2MainBundle:Batch');
    }

    /**
     * @Route(name="paints")
     */
    public function listPaints()
    {
        echo "404!";
        exit;
    }

    /**
     * @Route("/view1", name="paint_view1")
     */
    public function view1(Request $request)
    {
        $defaultParameters = array(
            'planner_esd' => null,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());

        $paints = $this->paintRepository->findPaint($parameters);

        return $this->render('paint/view_1.html.twig', array(
            'paints'        =>  $paints,
            'planner_esd'   =>  $parameters['planner_esd'],
        ));
    }

    /**
     * @Route("/view2", name="paint_view2")
     */
    public function view2(Request $request)
    {
        if ($request->isMethod('POST')) {
            $color = strtoupper($request->request->get('color'));
            $selectedPaintIds = $request->request->get('selected_paint');
            $paints = $this->paintRepository->findById($selectedPaintIds);
            $nextBatchNum = $this->batchRepository->getNextAvailableBatchNumber();
            return $this->render('paint/add_to_batch.html.twig', array(
                'color'         =>  $color,
                'paints'        =>  $paints,
                'nextBatchNum'  =>  $nextBatchNum,
            ));
        }        

        $defaultParameters = array(
            'name'          => null,
            'color'         => null,            
            'planner_esd'   => null,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());

        $paints = $this->paintRepository->findUnscheduledPaintJobs($parameters);

        return $this->render('paint/view_2.html.twig', array(
            'paints'        =>  $paints,
            'name'          =>  $parameters['name'],
            'color'         =>  $parameters['color'],
            'planner_esd'   =>  $parameters['planner_esd'],
        ));
    }

    /**
     * @Route("/addToBatch", name="paint_add_to_batch")
     */
    public function addToBatch(Request $request)
    {
        if ($request->isMethod('POST')) {
            $color = $request->request->get('color');
            $batchId = $request->request->get('next_batch_num');
            $batch = $this->batchRepository->find($batchId);
            if (!$batch) {
                $batch = new Batch();
            }
            $paints = $this->paintRepository->findById($request->request->get('selected_paint'));
            $batch->setColor($color);
            $batch->setRalColor($this->getRalColor($color));
            $batch->setEstimatedReleaseDate(new \DateTime($request->request->get('estimated_release_date')));
            $batch->setQuantity($request->request->get('quantity'));
            $batch->setVendor($request->request->get('vendor'));
            $batch->setUpdateTime(new \DateTime());
            $batch->setUpdatedBy($this->getUser());
            $this->em->persist($batch);
            $this->em->flush();

            // Add paint job(s) to batch (and vice versa)
            foreach ($paints as $paint) {
                $batch->addPaint($paint);
                $this->em->persist($batch);
                // Figure out whether the color belongs to the Paint's batch #
                if ($paint->getColor1() == $color && !$paint->getBatch1()) {
                    $paint->setBatch1($batch);
                } else {
                    $paint->setBatch2($batch);
                }
                $paint->setUpdateTime(new \DateTime());
                $paint->setUpdatedBy($this->getUser());
                $this->em->persist($paint);
            }
            $this->em->flush();

            $this->addFlash('success', 'Added Paint(s) to Batch!');
            return $this->redirect($this->generateUrl('paint_view2'));
        }

        // Error! (Should not get here)
        $this->addFlash('error', 'What are you trying to do?');
        return $this->redirect($this->generateUrl('dashboard'));        
    }

    /**
     * @Route("/view3", name="paint_view3")
     */
    public function view3(Request $request)
    {
        $defaultParameters = array(
            'color'                     => null,
            'vendor'                    => null,
            'estimated_release_date'    => null,
            'batch'                     => null,
            'show_all_batches'          => null,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());

        $batches = $this->batchRepository->findUnreceivedScheduledBatches($parameters);

        return $this->render('paint/view_3.html.twig', array(
            'batches'                   =>  $batches,
            'color'                     =>  $parameters['color'],
            'vendor'                    =>  $parameters['vendor'],
            'estimated_release_date'    =>  $parameters['estimated_release_date'],
            'batch'                     =>  $parameters['batch'],
            'show_all_batches'          =>  $parameters['show_all_batches'],
        ));
    }

    /**
     * @Route("/view3/print", name="print_paint_view3")
     */
    public function printView3(Request $request)
    {
        $defaultParameters = array(
            'color'                     => null,
            'vendor'                    => null,
            'estimated_release_date'    => null,
            'show_all_batches'          => null,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());

        $batches = $this->batchRepository->findUnreceivedScheduledBatches($parameters);

        return $this->render('paint/print_view_3.html.twig', array(
            'batches'                   =>  $batches,
            'color'                     =>  $parameters['color'],
            'vendor'                    =>  $parameters['vendor'],
            'estimated_release_date'    =>  $parameters['estimated_release_date'],
            'show_all_batches'          =>  $parameters['show_all_batches'],
        ));
    }

    /**
     * @Route("/view4", name="paint_view4")
     */
    public function view4(Request $request)
    {
        $defaultParameters = array(
            'name'           => null,
            'batch'          => null,
            'color'          => null,
            'v2_po_number'   => null,
            'planner_esd'    => null,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());

        $paints = $this->paintRepository->findJobsInBatch($parameters);

        return $this->render('paint/view_4.html.twig', array(
            'paints'        =>  $paints,
            'name'          =>  $parameters['name'],
            'batch'         =>  $parameters['batch'],
            'color'         =>  $parameters['color'],
            'v2_po_number'  =>  $parameters['v2_po_number'],
            'planner_esd'   =>  $parameters['planner_esd'],
        ));
    }

    /**
     * @Route("/export/paint", name="export_paint")
     */
    public function exportPaint(Request $request)
    {
        $defaultParameters = array(
            'name'           => null,
            'batch'          => null,
            'color'          => null,
            'v2_po_number'   => null,
            'planner_esd'    => null,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());
        $paints = $this->paintRepository->findJobsInBatch($parameters);
        // Convert to array before it can be encoded into CSV format
        $dataArray = array();
        foreach ($paints as $paint) {
            $paintArray = array();
            $paintArray['planner_esd'] = $paint->getJob()->getPlannerEstimatedShipDate() ? $paint->getJob()->getPlannerEstimatedShipDate()->format('Y-m-d') : null;
            $paintArray['job_name'] = $paint->getJob()->getName();
            $paintArray['type'] = $paint->getJob()->getType();
            $paintArray['sales_order'] = $paint->getJob()->getSalesOrder();
            $paintArray['quantity'] = $paint->getJob()->getQuantity();
            $paintArray['color1'] = $paint->getColor1();
            $paintArray['batch1'] = $paint->getBatch1() ? $paint->getBatch1()->getId() : null;
            $paintArray['batch1_po'] = $paint->getBatch1() ? $paint->getBatch1()->getV2PoNumber() : null;
            $paintArray['color2'] = $paint->getColor2();
            $paintArray['batch2'] = $paint->getBatch2() ? $paint->getBatch2()->getId() : null;
            $paintArray['batch2_po'] = $paint->getBatch2() ? $paint->getBatch2()->getV2PoNumber() : null;
            $paintArray['location'] = $paint->getLocation();
            $dataArray[] = $paintArray;
        }
        $serializer = $this->get('serializer');
        $csvData = $serializer->encode($dataArray, 'csv');

        // Downloads the file to the user's computer
        $response = new Response();
        $response->headers->set('Content-type', 'text/csv');
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-Disposition', 'attachment; filename=paint.csv;');
        $response->sendHeaders();
        $response->setContent($csvData);
        return $response;

    }

    private function getRalColor($color)
    {
        switch (strtoupper($color)) {
            case "C1" : return "038/00001";
            case "C2" : return "038/00088";
            case "A1" : return "061/00015";
            case "A2" : return "061/78003";
            case "A3" : return "061/78002";
            case "A4" : return "061/68002";
            case "A5" : return "061/68001";
            case "A6" : return "061/80079";
            case "M1" : return "038/90003";
            case "M2" : return "038/50035";
            case "M3" : return "038/50043";
            case "M4" : return "038/90016";
            case "M5" : return "038/90011";
            case "M6" : return "038/90007";
            case "M7" : return "038/60064";
            case "M8" : return "038/20014";
            case "L1" : return "049/11111";
            case "L2" : return "038/70035";
            case "L3" : return "049/92900";
            case "L4" : return "049/88888";
            case "T1" : return "049/13150";
            case "T2" : return "ral 7035";
            case "T3" : return "049/70789";
            case "T4" : return "038/80200";
            case "S1" : return "049/25006";
            case "S2" : return "049/23531";
            case "S3" : return "049/30028";
            case "S4" : return "049/30193";
            case "S5" : return "049/41100";
            case "S6" : return "049/40233";
            case "S7" : return "049/40074";
            case "S8" : return "049/0032";
            default : return "custom";
        }
    }

    /**
     * @Route("/{paintId}/editColor1", name="edit_paint_color1")
     */
    public function editPaintColor1(Request $request, $paintId)
    {
        $color = $request->request->get('value');

        $paint = $this->paintRepository->find($paintId);
        if (!$paint) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid Paint ID"));
        }

        if (is_null($color) || $color == '') {
            $paint->setColor1(null);
        } else {
            $paint->setColor1($color);
        }
        $paint->setUpdateTime(new \DateTime());
        $paint->setUpdatedBy($this->getUser());
        $this->em->persist($paint);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/{paintId}/editColor2", name="edit_paint_color2")
     */
    public function editPaintColor2(Request $request, $paintId)
    {
        $color = $request->request->get('value');

        $paint = $this->paintRepository->find($paintId);
        if (!$paint) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid Paint ID"));
        }

        if (is_null($color) || $color == '') {
            $paint->setColor2(null);
        } else {
            $paint->setColor2($color);
        }
        $paint->setUpdateTime(new \DateTime());
        $paint->setUpdatedBy($this->getUser());
        $this->em->persist($paint);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("/{paintId}/editBatch1", name="edit_paint_batch1")
     */
    public function editPaintBatch1(Request $request, $paintId)
    {
        $batchId = $request->request->get('value');

        $paint = $this->paintRepository->find($paintId);
        if (!$paint) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid Paint ID"));
        }
        // Error checking in case user tries to set a batch ID without color
        if (!$paint->getColor1()) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid Paint Color"));   
        }

        // Check if the paint has been already assigned to another batch
        $oldBatch = $paint->getBatch1();
        if ($oldBatch) {
            // if so we need to remove it
            $oldBatch->removePaint($paint);
            $this->em->persist($oldBatch);
        }

        // Handle case user removes the batch
        if (is_null($batchId) || $batchId == '') {
            $paint->setBatch1(null);
            $paint->setUpdateTime(new \DateTime());
            $paint->setUpdatedBy($this->getUser());
            $this->em->persist($paint);
            $this->em->flush();
            // Immediately return
            return $this->json(array('status' => 'success'));
        }

        $batch = $this->batchRepository->find($batchId);
        if (!$batch) {
            $batch = new Batch();
        }
        $batch->addPaint($paint);
        $batch->setColor($paint->getColor1());
        $batch->setRalColor($this->getRalColor($paint->getColor1()));
        $batch->setUpdateTime(new \DateTime());
        $batch->setUpdatedBy($this->getUser());
        $this->em->persist($batch);
        $this->em->flush();

        $paint->setBatch1($batch);
        $paint->setUpdateTime(new \DateTime());
        $paint->setUpdatedBy($this->getUser());
        $this->em->persist($paint);
        $this->em->flush();

        return $this->json(array('status' => 'success', 'quantity' => $batch->getQuantity()));
    }

    /**
     * @Route("/{paintId}/editBatch2", name="edit_paint_batch2")
     */
    public function editPaintBatch2(Request $request, $paintId)
    {
        $batchId = $request->request->get('value');

        $paint = $this->paintRepository->find($paintId);

        if (!$paint) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid Paint ID"));
        }
        // Error checking in case user tries to set a batch ID without color
        if (!$paint->getColor2()) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid Paint Color"));   
        }

        // Check if the paint has been already assigned to another batch
        $oldBatch = $paint->getBatch2();
        if ($oldBatch) {
            // if so we need to remove it
            $oldBatch->removePaint($paint);
            $this->em->persist($oldBatch);
        }

        // Handle case user removes the batch
        if (is_null($batchId) || $batchId == '') {
            $paint->setBatch2(null);
            $paint->setUpdateTime(new \DateTime());
            $paint->setUpdatedBy($this->getUser());
            $this->em->persist($paint);
            $this->em->flush();
            // Immediately return
            return $this->json(array('status' => 'success'));
        }

        $batch = $this->batchRepository->find($batchId);
        if (!$batch) {
            $batch = new Batch();
        }

        $batch->addPaint($paint);
        $batch->setColor($paint->getColor2());
        $batch->setRalColor($this->getRalColor($paint->getColor2()));
        $batch->setUpdateTime(new \DateTime());
        $batch->setUpdatedBy($this->getUser());
        $this->em->persist($batch);
        $this->em->flush();

        $paint->setBatch2($batch);
        $paint->setUpdateTime(new \DateTime());
        $paint->setUpdatedBy($this->getUser());
        $this->em->persist($paint);
        $this->em->flush();

        return $this->json(array('status' => 'success', 'quantity' => $batch->getQuantity()));
    }

    /**
     * @Route("/{paintId}/editLocation", name="edit_paint_location")
     */
    public function editPaintLocation(Request $request, $paintId)
    {
        $location = $request->request->get('value');

        $paint = $this->paintRepository->find($paintId);

        if (!$paint) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid Paint ID"));
        }
        $paint->setLocation($location);
        $paint->setUpdateTime(new \DateTime());
        $paint->setUpdatedBy($this->getUser());
        $this->em->persist($paint);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("batch/{batchId}/editEstimatedReleaseDate", name="edit_batch_estimated_release_date")
     */
    public function editBatchEstimatedReleaseDate(Request $request, $batchId)
    {
        $estimatedReleaseDate = $request->request->get('value');

        $batch = $this->batchRepository->find($batchId);
        if (!$batch) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid Batch ID"));
        }

        if (is_null($estimatedReleaseDate) || $estimatedReleaseDate == '') {
            $batch->setEstimatedReleaseDate(null);
        } else {
            $batch->setEstimatedReleaseDate(new \DateTime($estimatedReleaseDate));
        }
        $batch->setUpdateTime(new \DateTime());
        $batch->setUpdatedBy($this->getUser());
        $this->em->persist($batch);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("batch/{batchId}/editNotes", name="edit_batch_notes")
     */
    public function editBatchNotes(Request $request, $batchId)
    {
        $notes = $request->request->get('value');

        $batch = $this->batchRepository->find($batchId);
        if (!$batch) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid Batch ID"));
        }
        $batch->setNotes($notes);
        $batch->setUpdateTime(new \DateTime());
        $batch->setUpdatedBy($this->getUser());
        $this->em->persist($batch);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("batch/{batchId}/editVendor", name="edit_batch_vendor")
     */
    public function editBatchVendor(Request $request, $batchId)
    {
        $vendor = $request->request->get('value');

        $batch = $this->batchRepository->find($batchId);
        if (!$batch) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid Batch ID"));
        }
        $batch->setVendor($vendor);
        $batch->setUpdateTime(new \DateTime());
        $batch->setUpdatedBy($this->getUser());
        $this->em->persist($batch);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("batch/{batchId}/editV2PoNumber", name="edit_batch_v2_po_number")
     */
    public function editBatchV2PoNumber(Request $request, $batchId)
    {
        $v2PoNumber = $request->request->get('value');

        $batch = $this->batchRepository->find($batchId);
        if (!$batch) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid Batch ID"));
        }
        $batch->setV2PoNumber($v2PoNumber);
        $batch->setUpdateTime(new \DateTime());
        $batch->setUpdatedBy($this->getUser());
        $this->em->persist($batch);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("batch/{batchId}/editNeededByDate", name="edit_batch_needed_by_date")
     */
    public function editBatchNeededByDate(Request $request, $batchId)
    {
        $neededByDate = $request->request->get('value');

        $batch = $this->batchRepository->find($batchId);
        if (!$batch) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid Batch ID"));
        }

        if (is_null($neededByDate) || $neededByDate == '') {
            $batch->setNeededByDate(null);
        } else {
            $batch->setNeededByDate(new \DateTime($neededByDate));
        }
        $batch->setUpdateTime(new \DateTime());
        $batch->setUpdatedBy($this->getUser());
        $this->em->persist($batch);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("batch/{batchId}/editKitDate", name="edit_batch_kit_date")
     */
    public function editBatchKitDate(Request $request, $batchId)
    {
        $kitDate = $request->request->get('value');

        $batch = $this->batchRepository->find($batchId);
        if (!$batch) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid Batch ID"));
        }

        if (is_null($kitDate) || $kitDate == '') {
            $batch->setKitDate(null);
        } else {
            $batch->setKitDate(new \DateTime($kitDate));
        }
        $batch->setUpdateTime(new \DateTime());
        $batch->setUpdatedBy($this->getUser());
        $this->em->persist($batch);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("batch/{batchId}/editEstimatedDeliveryDate", name="edit_batch_estimated_delivery_date")
     */
    public function editBatchEstimatedDeliveryDate(Request $request, $batchId)
    {
        $estimatedDeliveryDate = $request->request->get('value');

        $batch = $this->batchRepository->find($batchId);
        if (!$batch) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid Batch ID"));
        }

        if (is_null($estimatedDeliveryDate) || $estimatedDeliveryDate == '') {
            $batch->setEstimatedDeliveryDate(null);
        } else {
            $batch->setEstimatedDeliveryDate(new \DateTime($estimatedDeliveryDate));
        }
        $batch->setUpdateTime(new \DateTime());
        $batch->setUpdatedBy($this->getUser());
        $this->em->persist($batch);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("batch/{batchId}/editReceivedDate", name="edit_batch_received_date")
     */
    public function editBatchReceivedDate(Request $request, $batchId)
    {
        $receivedDate = $request->request->get('value');

        $batch = $this->batchRepository->find($batchId);
        if (!$batch) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid Batch ID"));
        }
        if (is_null($receivedDate) || $receivedDate == '') {
            $batch->setReceivedDate(null);
        } else {
            $batch->setReceivedDate(new \DateTime($receivedDate));
        }
        $batch->setUpdateTime(new \DateTime());
        $batch->setUpdatedBy($this->getUser());
        $this->em->persist($batch);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

    /**
     * @Route("batch/{batchId}/editQuantity", name="edit_batch_quantity")
     */
    public function editBatchQuantity(Request $request, $batchId)
    {
        $quantity = $request->request->get('value');

        $batch = $this->batchRepository->find($batchId);
        if (!$batch) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid Batch ID"));
        }
        $batch->setQuantity($quantity);
        $batch->setUpdateTime(new \DateTime());
        $batch->setUpdatedBy($this->getUser());
        $this->em->persist($batch);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

}

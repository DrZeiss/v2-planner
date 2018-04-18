<?php

namespace V2\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
            $selectedPaintIds = $request->request->get('selected_paint');
            $paints = $this->paintRepository->findById($selectedPaintIds);
            $nextBatchNum = $this->batchRepository->getNextAvailableBatchNumber();
            return $this->render('paint/add_to_batch.html.twig', array(
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
            $batchId = $request->request->get('next_batch_num');
            $batch = $this->batchRepository->find($batchId);
            if (!$batch) {
                $batch = new Batch();
            }
            $paints = $this->paintRepository->findById($request->request->get('selected_paint'));
            // Get the color (only use the color that doesn't have a Batch ID yet)
            $color = $paints[0]->getBatch1() ? $paints[0]->getColor2() : $paints[0]->getColor1();
            $batch->setColor($color);
            $batch->setRalColor($this->getRalColor($color));
            $batch->setNeededByDate(new \DateTime($request->request->get('needed_by_date')));
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
                if ($paint->getBatch1()) {
                    $paint->setBatch2($batch);
                } else {
                    $paint->setBatch1($batch);
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
            'show_all_batches'          => null,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());

        $batches = $this->batchRepository->findUnreceivedScheduledBatches($parameters);

        return $this->render('paint/view_3.html.twig', array(
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
            'planner_esd'    => null,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());

        $paints = $this->paintRepository->findJobsInBatch($parameters);

        return $this->render('paint/view_4.html.twig', array(
            'paints'        =>  $paints,
            'name'          =>  $parameters['name'],
            'batch'         =>  $parameters['batch'],
            'color'         =>  $parameters['color'],
            'planner_esd'   =>  $parameters['planner_esd'],
        ));
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
            case "L1" : return "049/20014";
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
     * @Route("/{paintId}/editBatch1", name="edit_paint_batch1")
     */
    public function editPaintBatch1(Request $request, $paintId)
    {
        $batchId = $request->request->get('value');

        $paint = $this->paintRepository->find($paintId);
        if (!$paint) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid Paint ID"));
        }

        $batch = $this->batchRepository->find($batchId);
        if (!$batch) {
            $batch = new Batch();
            $batch->setPaint($paint);
        }
        // We only set the batch quantity when the first time it is assigned to a batch
        // All other changes is up to user
        if (!$paint->getBatch1()) {
            $batch->addQuantity($paint->getJob()->getQuantity());
        }
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
        $batch = $this->batchRepository->find($batchId);
        if (!$batch) {
            $batch = new Batch();
            $batch->setPaint($paint);
        }
        // We only set the batch quantity when the first time it is assigned to a batch
        // All other changes is up to user
        if (!$paint->getBatch2()) {
            $batch->addQuantity($paint->getJob()->getQuantity());
        }

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
        $batch->setEstimatedReleaseDate(new \DateTime($estimatedReleaseDate));
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
     * @Route("batch/{batchId}/editKitDate", name="edit_batch_kit_date")
     */
    public function editBatchKitDate(Request $request, $batchId)
    {
        $kitDate = $request->request->get('value');

        $batch = $this->batchRepository->find($batchId);
        if (!$batch) {
            return $this->json(array('status' => 'error', 'msg' => "Invalid Batch ID"));
        }
        $batch->setKitDate(new \DateTime($kitDate));
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
        $batch->setEstimatedDeliveryDate(new \DateTime($estimatedDeliveryDate));
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
        $batch->setReceivedDate(new \DateTime($receivedDate));
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

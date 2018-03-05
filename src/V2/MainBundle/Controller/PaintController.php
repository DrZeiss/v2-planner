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
        $batch->setColor($paint->getColor1());
        $batch->setRalColor($paint->getColor1());
        $batch->setUpdateTime(new \DateTime());
        $batch->setUpdatedBy($this->getUser());
        $this->em->persist($batch);
        $this->em->flush();

        $paint->setBatch1($batch);
        $paint->setUpdateTime(new \DateTime());
        $paint->setUpdatedBy($this->getUser());
        $this->em->persist($paint);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
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
        $batch->setColor($paint->getColor2());
        $batch->setRalColor($paint->getColor2());
        $batch->setUpdateTime(new \DateTime());
        $batch->setUpdatedBy($this->getUser());
        $this->em->persist($batch);
        $this->em->flush();

        $paint->setBatch2($batch);
        $paint->setUpdateTime(new \DateTime());
        $paint->setUpdatedBy($this->getUser());
        $this->em->persist($paint);
        $this->em->flush();

        return $this->json(array('status' => 'success'));
    }

}

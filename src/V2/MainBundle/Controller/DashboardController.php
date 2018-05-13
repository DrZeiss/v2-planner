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
        // Get the jobs scheduled (by week)
        $jobsLastWeek = $this->em->getRepository('V2MainBundle:Job')->getJobsLastWeek();
        $jobsByWeek = $this->em->getRepository('V2MainBundle:Job')->getJobsByWeek();
        $jobsByWeek = array_merge($jobsLastWeek, $jobsByWeek);
        $currentWeek = date('W');
        $weekTexts = array();
        $jobsByWeekTexts = array();
        $rushJobsByWeekTexts = array();
        $shippedJobsByWeekTexts = array();
        foreach ($jobsByWeek as $index => $week) {
            $shippedJobsByWeekTexts[$index] = 0;
            if ($week['week_num'] == $currentWeek - 1) {
                $weekTexts[$index] = 'Last week';
            } else if ($week['week_num'] == $currentWeek) {
                $weekTexts[$index] = 'This week';
                $jobsShippedThisWeek = $this->em->getRepository('V2MainBundle:Job')->getJobsShippedThisWeek();
                $shippedJobsByWeekTexts[$index] = $jobsShippedThisWeek[0]['num_shipped_jobs'];
            } else if ($week['week_num'] == $currentWeek + 1) {
                $weekTexts[$index] = 'Next week';
            } else {
                $weekTexts[$index] = 'Week ' . $week['week_num'];
            }
            $jobsByWeekTexts[$index] = $week['num_jobs'];
            $rushJobsByWeekTexts[$index] = $week['num_rush_jobs'];
        }

        // Get number of open jobs
        $jobStats = $this->em->getRepository('V2MainBundle:Job')->getJobStats()[0];
        $totalOpenJobs = $jobStats['total'];

        // Get Production load by location
        $productionByWeek = $this->em->getRepository('V2MainBundle:Job')->getProductionByWeek();
        $totalFixturesToBuild = 0;
        $productionV2ByWeek = array();
        $productionMacByWeek = array();
        foreach ($productionByWeek as $index => $week) {
            $productionV2ByWeek[$index] = $week['num_v2_fixtures'];
            $productionMacByWeek[$index] = $week['num_mac_fixtures'];
            // Get total too
            $totalFixturesToBuild += $week['num_v2_fixtures'] + $week['num_mac_fixtures'];
        }
        // Get Fixtures shipped by location (this month)
        $fixturesShippedThisMonth = $this->em->getRepository('V2MainBundle:Job')->getFixturesShippedThisMonth();

        // Get Scheduling by Job Life Stage
        $jobsByStage = $this->em->getRepository('V2MainBundle:Job')->getJobsByStage();
        $count = 0;
        $stageTexts = array();
        $jobsByStageTexts = array();
        $jobsClearToBuildByStageTexts = array();
        foreach ($jobsByStage as $stageName => $jobs) {
            $stageTexts[$count] = $stageName;
            $jobsClearToBuildByStageTexts[$count] = count($this->em->getRepository('V2MainBundle:Job')->getJobsClearToBuild($stageName));
            $jobsByStageTexts[$count] = count($jobs) - $jobsClearToBuildByStageTexts[$count];
            $count++;
        }

        // Get number of jobs released last week
        $jobsReleasedLastWeek = $this->em->getRepository('V2MainBundle:Job')->getNumJobReleasedByWeeks(1);
        // Get number of shipped jobs last week
        $jobsShippedLastWeek = $this->em->getRepository('V2MainBundle:Job')->getNumJobShippedByWeeks(1);

        // Get late jobs (job name and ESD)
        $lateJobs = $this->em->getRepository('V2MainBundle:Job')->getLateJobs();

        return $this->render('dashboard/index.html.twig', array(
            'currentWeek' => $currentWeek,
            'weeks' => json_encode($weekTexts),
            'jobsByWeek' => json_encode($jobsByWeekTexts),
            'rushJobsByWeek' => json_encode($rushJobsByWeekTexts),
            'shippedJobsByWeek' => json_encode($shippedJobsByWeekTexts),
            'totalOpenJobs' => $totalOpenJobs,
            'productionV2ByWeek' => json_encode($productionV2ByWeek),
            'productionMacByWeek' => json_encode($productionMacByWeek),
            'totalFixturesToBuild' => $totalFixturesToBuild,
            'fixturesShippedThisMonth' => $fixturesShippedThisMonth,
            'stageTexts' => json_encode($stageTexts),
            'jobsByStageTexts' => json_encode($jobsByStageTexts),
            'jobsClearToBuildByStageTexts' => json_encode($jobsClearToBuildByStageTexts),
            'jobsReleasedLastWeek' => $jobsReleasedLastWeek,
            'jobsShippedLastWeek' => $jobsShippedLastWeek,
            'lateJobs' => $lateJobs,
        ));
    }
}

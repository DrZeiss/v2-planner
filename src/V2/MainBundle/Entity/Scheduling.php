<?php

namespace V2\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Scheduling
 *
 * @ORM\Table(name="scheduling")
 * @ORM\Entity(repositoryClass="V2\MainBundle\Repository\SchedulingRepository")
 */
class Scheduling
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="V2\MainBundle\Entity\Job")
     * @ORM\JoinColumn(name="job_id", referencedColumnName="id")
     */
    private $job;

    /**
     * -1 = extra
     * 0 = normal
     * 1 = custom
     * 2 = hot
     * 3 = rush
     * 4 = RMA
     *
     * @var int
     *
     * @ORM\Column(name="priority", type="integer")
     */
    private $priority;

    /**
     * @var int
     *
     * @ORM\Column(name="priority_bom_builder", type="integer")
     */
    private $priorityBomBuilder;

    /**
     * @var int
     *
     * @ORM\Column(name="priority_kitter", type="integer")
     */
    private $priorityKitter;

    /**
     * @var int
     *
     * @ORM\Column(name="priority_mac_production", type="integer")
     */
    private $priorityMacProduction;

    /**
     * @var int
     *
     * @ORM\Column(name="priority_v2_production", type="integer")
     */
    private $priorityV2Production;

    /**
     * @var int
     *
     * @ORM\Column(name="priority_shipper", type="integer")
     */
    private $priorityShipper;

    /**
     * @var bool
     *
     * @ORM\Column(name="sub_ready", type="boolean")
     */
    private $subReady;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="completion_date", type="datetime", nullable=true)
     */
    private $completionDate;

    /**
     * @var string
     *
     * @ORM\Column(name="built_by", type="string", length=255, nullable=true)
     */
    private $builtBy;

    /**
     * @ORM\Column(name="update_time", type="datetime")
     */
    private $updateTime;

    /**
     * @ORM\ManyToOne(targetEntity="V2\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="user_id")
     */
    private $updatedBy;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->priority = 0;
        $this->priorityBomBuilder = 0;
        $this->priorityKitter = 0;
        $this->priorityMacProduction = 0;
        $this->priorityV2Production = 0;
        $this->priorityShipper = 0;
        $this->subReady = false;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     *
     * @return Scheduling
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set priorityBomBuilder
     *
     * @param integer $priorityBomBuilder
     *
     * @return Scheduling
     */
    public function setPriorityBomBuilder($priorityBomBuilder)
    {
        $this->priorityBomBuilder = $priorityBomBuilder;

        return $this;
    }

    /**
     * Get priorityBomBuilder
     *
     * @return int
     */
    public function getPriorityBomBuilder()
    {
        return $this->priorityBomBuilder;
    }

    /**
     * Set priorityKitter
     *
     * @param integer $priorityKitter
     *
     * @return Scheduling
     */
    public function setPriorityKitter($priorityKitter)
    {
        $this->priorityKitter = $priorityKitter;

        return $this;
    }

    /**
     * Get priorityKitter
     *
     * @return int
     */
    public function getPriorityKitter()
    {
        return $this->priorityKitter;
    }

    /**
     * Set priorityMacProduction
     *
     * @param integer $priorityMacProduction
     *
     * @return Scheduling
     */
    public function setPriorityMacProduction($priorityMacProduction)
    {
        $this->priorityMacProduction = $priorityMacProduction;

        return $this;
    }

    /**
     * Get priorityMacProduction
     *
     * @return int
     */
    public function getPriorityMacProduction()
    {
        return $this->priorityMacProduction;
    }

    /**
     * Set priorityV2Production
     *
     * @param integer $priorityV2Production
     *
     * @return Scheduling
     */
    public function setPriorityV2Production($priorityV2Production)
    {
        $this->priorityV2Production = $priorityV2Production;

        return $this;
    }

    /**
     * Get priorityV2Production
     *
     * @return int
     */
    public function getPriorityV2Production()
    {
        return $this->priorityV2Production;
    }

    /**
     * Set priorityShipper
     *
     * @param integer $priorityShipper
     *
     * @return Scheduling
     */
    public function setPriorityShipper($priorityShipper)
    {
        $this->priorityShipper = $priorityShipper;

        return $this;
    }

    /**
     * Get priorityShipper
     *
     * @return int
     */
    public function getPriorityShipper()
    {
        return $this->priorityShipper;
    }

    /**
     * Set subReady
     *
     * @param boolean $subReady
     *
     * @return Scheduling
     */
    public function setSubReady($subReady)
    {
        $this->subReady = $subReady;

        return $this;
    }

    /**
     * Get subReady
     *
     * @return bool
     */
    public function getSubReady()
    {
        return $this->subReady;
    }

    /**
     * Set completionDate
     *
     * @param \DateTime $completionDate
     *
     * @return Scheduling
     */
    public function setCompletionDate($completionDate)
    {
        $this->completionDate = $completionDate;

        return $this;
    }

    /**
     * Get completionDate
     *
     * @return \DateTime
     */
    public function getCompletionDate()
    {
        return $this->completionDate;
    }

    /**
     * Set builtBy
     *
     * @param string $builtBy
     *
     * @return Job
     */
    public function setBuiltBy($builtBy)
    {
        $this->builtBy = $builtBy;

        return $this;
    }

    /**
     * Get builtBy
     *
     * @return string
     */
    public function getBuiltBy()
    {
        return $this->builtBy;
    }

    /**
     * Set updateTime
     *
     * @param \DateTime $updateTime
     *
     * @return Scheduling
     */
    public function setUpdateTime($updateTime)
    {
        $this->updateTime = $updateTime;

        return $this;
    }

    /**
     * Get updateTime
     *
     * @return \DateTime
     */
    public function getUpdateTime()
    {
        return $this->updateTime;
    }

    /**
     * Set job
     *
     * @param \V2\MainBundle\Entity\Job $job
     *
     * @return Scheduling
     */
    public function setJob(\V2\MainBundle\Entity\Job $job = null)
    {
        $this->job = $job;

        return $this;
    }

    /**
     * Get job
     *
     * @return \V2\MainBundle\Entity\Job
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * Set updatedBy
     *
     * @param \V2\UserBundle\Entity\User $updatedBy
     *
     * @return Scheduling
     */
    public function setUpdatedBy(\V2\UserBundle\Entity\User $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \V2\UserBundle\Entity\User
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

}

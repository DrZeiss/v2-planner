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
     * @var int
     *
     * @ORM\Column(name="priority", type="integer")
     */
    private $priority;

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

<?php

namespace V2\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Kitting
 *
 * @ORM\Table(name="kitting", indexes={@ORM\Index(name="filled_completely_idx", columns={"filled_completely"})})
 * @ORM\Entity(repositoryClass="V2\MainBundle\Repository\KittingRepository")
 */
class Kitting
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
     * @var \DateTime
     *
     * @ORM\Column(name="completion_date", type="datetime", nullable=true)
     */
    private $completionDate;

    /**
     * @var bool
     *
     * @ORM\Column(name="filled_completely", type="boolean", nullable=true)
     */
    private $filledCompletely;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * 0: No
     * 1: Yes
     * 2: Done
     *
     * @ORM\Column(name="epoxy", type="integer", nullable=true)
     */
    private $epoxy;

    /**
     * @var bool
     *
     * @ORM\Column(name="epoxy_needed", type="boolean")
     */
    private $epoxyNeeded;

    /**
     * @var bool
     *
     * @ORM\Column(name="epoxy_done", type="boolean", nullable=true)
     */
    private $epoxyDone;

    /**
     * 0: No
     * 1: Yes
     * 2: Done
     *
     * @ORM\Column(name="tube", type="integer", nullable=true)
     */
    private $tube;

    /**
     * @var bool
     *
     * @ORM\Column(name="tube_needed", type="boolean")
     */
    private $tubeNeeded;

    /**
     * @var bool
     *
     * @ORM\Column(name="tube_done", type="boolean", nullable=true)
     */
    private $tubeDone;

    /**
     * @ORM\ManyToOne(targetEntity="V2\MainBundle\Entity\KittingShort")
     * @ORM\JoinColumn(name="kitting_short_1_id", referencedColumnName="id")
     */
    private $kittingShort1;

    /**
     * @ORM\ManyToOne(targetEntity="V2\MainBundle\Entity\KittingShort")
     * @ORM\JoinColumn(name="kitting_short_2_id", referencedColumnName="id")
     */
    private $kittingShort2;

    /**
     * @ORM\ManyToOne(targetEntity="V2\MainBundle\Entity\KittingShort")
     * @ORM\JoinColumn(name="kitting_short_3_id", referencedColumnName="id")
     */
    private $kittingShort3;

    /**
     * @ORM\ManyToOne(targetEntity="V2\MainBundle\Entity\KittingShort")
     * @ORM\JoinColumn(name="kitting_short_4_id", referencedColumnName="id")
     */
    private $kittingShort4;

    /**
     * @ORM\Column(name="create_time", type="datetime")
     */
    private $createTime;

    /**
     * @ORM\ManyToOne(targetEntity="V2\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="user_id")
     */
    private $createdBy;

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
        $this->epoxyNeeded= false;
        $this->tubeNeeded = false;
        $this->createTime = new \DateTime();
        $this->updateTime = new \DateTime();
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
     * Set completionDate
     *
     * @param \DateTime $completionDate
     *
     * @return Kitting
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
     * Set filledCompletely
     *
     * @param boolean $filledCompletely
     *
     * @return Kitting
     */
    public function setFilledCompletely($filledCompletely)
    {
        $this->filledCompletely = $filledCompletely;

        return $this;
    }

    /**
     * Get filledCompletely
     *
     * @return bool
     */
    public function getFilledCompletely()
    {
        return $this->filledCompletely;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return Kitting
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set epoxy
     *
     * @param int $epoxy
     *
     * @return Kitting
     */
    public function setEpoxy($epoxy)
    {
        $this->epoxy = $epoxy;

        return $this;
    }

    /**
     * Get epoxy
     *
     * @return int
     */
    public function getEpoxy()
    {
        return $this->epoxy;
    }

    /**
     * Set tube
     *
     * @param int $tube
     *
     * @return Kitting
     */
    public function setTube($tube)
    {
        $this->tube = $tube;

        return $this;
    }

    /**
     * Get tube
     *
     * @return int
     */
    public function getTube()
    {
        return $this->tube;
    }

    /**
     * Set epoxyNeeded
     *
     * @param boolean $epoxyNeeded
     *
     * @return Kitting
     */
    public function setEpoxyNeeded($epoxyNeeded)
    {
        $this->epoxyNeeded = $epoxyNeeded;

        return $this;
    }

    /**
     * Get epoxyNeeded
     *
     * @return bool
     */
    public function getEpoxyNeeded()
    {
        return $this->epoxyNeeded;
    }

    /**
     * Set epoxyDone
     *
     * @param boolean $epoxyDone
     *
     * @return Kitting
     */
    public function setEpoxyDone($epoxyDone)
    {
        $this->epoxyDone = $epoxyDone;

        return $this;
    }

    /**
     * Get epoxyDone
     *
     * @return bool
     */
    public function getEpoxyDone()
    {
        return $this->epoxyDone;
    }

    /**
     * Set tubeNeeded
     *
     * @param boolean $tubeNeeded
     *
     * @return Kitting
     */
    public function setTubeNeeded($tubeNeeded)
    {
        $this->tubeNeeded = $tubeNeeded;

        return $this;
    }

    /**
     * Get tubeNeeded
     *
     * @return bool
     */
    public function getTubeNeeded()
    {
        return $this->tubeNeeded;
    }

    /**
     * Set tubeDone
     *
     * @param boolean $tubeDone
     *
     * @return Kitting
     */
    public function setTubeDone($tubeDone)
    {
        $this->tubeDone = $tubeDone;

        return $this;
    }

    /**
     * Get tubeDone
     *
     * @return bool
     */
    public function getTubeDone()
    {
        return $this->tubeDone;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     *
     * @return Kitting
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;

        return $this;
    }

    /**
     * Get createTime
     *
     * @return \DateTime
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Set createdBy
     *
     * @param \V2\UserBundle\Entity\User $createdBy
     *
     * @return Kitting
     */
    public function setCreatedBy(\V2\UserBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \V2\UserBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updateTime
     *
     * @param \DateTime $updateTime
     *
     * @return Kitting
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
     * Set updatedBy
     *
     * @param \V2\UserBundle\Entity\User $updatedBy
     *
     * @return Kitting
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

    /**
     * Set job
     *
     * @param \V2\MainBundle\Entity\Job $job
     *
     * @return Kitting
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
     * Set kittingShort1
     *
     * @param \V2\MainBundle\Entity\KittingShort $kittingShort1
     *
     * @return Kitting
     */
    public function setKittingShort1(\V2\MainBundle\Entity\KittingShort $kittingShort1 = null)
    {
        $this->kittingShort1 = $kittingShort1;
        $this->kittingShort1->setKitting($this);
        return $this;
    }

    /**
     * Get kittingShort1
     *
     * @return \V2\MainBundle\Entity\KittingShort
     */
    public function getKittingShort1()
    {
        return $this->kittingShort1;
    }

    /**
     * Set kittingShort2
     *
     * @param \V2\MainBundle\Entity\KittingShort $kittingShort2
     *
     * @return Kitting
     */
    public function setKittingShort2(\V2\MainBundle\Entity\KittingShort $kittingShort2 = null)
    {
        $this->kittingShort2 = $kittingShort2;
        $this->kittingShort2->setKitting($this);
        return $this;
    }

    /**
     * Get kittingShort2
     *
     * @return \V2\MainBundle\Entity\KittingShort
     */
    public function getKittingShort2()
    {
        return $this->kittingShort2;
    }

    /**
     * Set kittingShort3
     *
     * @param \V2\MainBundle\Entity\KittingShort $kittingShort3
     *
     * @return Kitting
     */
    public function setKittingShort3(\V2\MainBundle\Entity\KittingShort $kittingShort3 = null)
    {
        $this->kittingShort3 = $kittingShort3;
        $this->kittingShort3->setKitting($this);
        return $this;
    }

    /**
     * Get kittingShort3
     *
     * @return \V2\MainBundle\Entity\KittingShort
     */
    public function getKittingShort3()
    {
        return $this->kittingShort3;
    }
    /**
     * Set kittingShort4
     *
     * @param \V2\MainBundle\Entity\KittingShort $kittingShort4
     *
     * @return Kitting
     */
    public function setKittingShort4(\V2\MainBundle\Entity\KittingShort $kittingShort4 = null)
    {
        $this->kittingShort4 = $kittingShort4;
        $this->kittingShort4->setKitting($this);
        return $this;
    }

    /**
     * Get kittingShort4
     *
     * @return \V2\MainBundle\Entity\KittingShort
     */
    public function getKittingShort4()
    {
        return $this->kittingShort4;
    }
}

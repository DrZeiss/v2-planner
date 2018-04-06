<?php

namespace V2\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Shipping
 *
 * @ORM\Table(name="shipping")
 * @ORM\Entity(repositoryClass="V2\MainBundle\Repository\ShippingRepository")
 */
class Shipping
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
     * @ORM\Column(name="ship_date", type="datetime", nullable=true)
     */
    private $shipDate;

    /**
     * 0: N/A
     * 1: Partial
     * 2: Complete
     *     
     * @ORM\Column(name="is_complete", type="integer")
     */
    private $isComplete;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="second_ship_date", type="datetime", nullable=true)
     */
    private $secondShipDate;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="string", length=255, nullable=true)
     */
    private $notes;

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
        $this->isComplete = 0;
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
     * Set job
     *
     * @param \V2\MainBundle\Entity\Job $job
     *
     * @return Bom
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
     * Set shipDate
     *
     * @param \DateTime $shipDate
     *
     * @return Shipping
     */
    public function setShipDate($shipDate)
    {
        $this->shipDate = $shipDate;

        return $this;
    }

    /**
     * Get shipDate
     *
     * @return \DateTime
     */
    public function getShipDate()
    {
        return $this->shipDate;
    }

    /**
     * Set isComplete
     *
     * @param boolean $isComplete
     *
     * @return Shipping
     */
    public function setIsComplete($isComplete)
    {
        $this->isComplete = $isComplete;

        return $this;
    }

    /**
     * Get isComplete
     *
     * @return bool
     */
    public function getIsComplete()
    {
        return $this->isComplete;
    }

    /**
     * Set secondShipDate
     *
     * @param \DateTime $secondShipDate
     *
     * @return Shipping
     */
    public function setSecondShipDate($secondShipDate)
    {
        $this->secondShipDate = $secondShipDate;

        return $this;
    }

    /**
     * Get secondShipDate
     *
     * @return \DateTime
     */
    public function getSecondShipDate()
    {
        return $this->secondShipDate;
    }
    
    /**
     * Set notes
     *
     * @param string $notes
     *
     * @return Shipping
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set updateTime
     *
     * @param \DateTime $updateTime
     *
     * @return Bom
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
     * @return Shipping
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
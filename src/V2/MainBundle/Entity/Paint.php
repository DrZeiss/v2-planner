<?php

namespace V2\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Paint
 *
 * @ORM\Table(name="paint")
 * @ORM\Entity(repositoryClass="V2\MainBundle\Repository\PaintRepository")
 */
class Paint
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
     * @var string
     *
     * @ORM\Column(name="color_1", type="string", length=255)
     */
    private $color1;

    /**
     * @ORM\ManyToOne(targetEntity="V2\MainBundle\Entity\Batch")
     * @ORM\JoinColumn(name="batch_1_id", referencedColumnName="id")
     */
    private $batch1;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="batch_1_edd", type="datetime", nullable=true)
     */
    private $batch1EstimatedDeliveryDate;

    /**
     * @var string
     *
     * @ORM\Column(name="color_2", type="string", length=255, nullable=true)
     */
    private $color2;
    
    /**
     * @ORM\ManyToOne(targetEntity="V2\MainBundle\Entity\Batch")
     * @ORM\JoinColumn(name="batch_2_id", referencedColumnName="id", nullable=true)
     */
    private $batch2;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="batch_2_edd", type="datetime", nullable=true)
     */
    private $batch2EstimatedDeliveryDate;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=255, nullable=true)
     */
    private $location;

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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set issuedDate
     *
     * @param \DateTime $issuedDate
     *
     * @return Paint
     */
    public function setIssuedDate($issuedDate)
    {
        $this->issuedDate = $issuedDate;

        return $this;
    }

    /**
     * Get issuedDate
     *
     * @return \DateTime
     */
    public function getIssuedDate()
    {
        return $this->issuedDate;
    }

    /**
     * Set issuedBy
     *
     * @param \V2\UserBundle\Entity\User $issuedBy
     *
     * @return Paint
     */
    public function setIssuedBy(\V2\UserBundle\Entity\User $issuedBy = null)
    {
        $this->issuedBy = $issuedBy;

        return $this;
    }

    /**
     * Get issuedBy
     *
     * @return \V2\UserBundle\Entity\User
     */
    public function getIssuedBy()
    {
        return $this->issuedBy;
    }

    /**
     * Set job
     *
     * @param \V2\MainBundle\Entity\Job $job
     *
     * @return Paint
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
     * Set updateTime
     *
     * @param \DateTime $updateTime
     *
     * @return Paint
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
     * @return Paint
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
     * Set serialsGeneratedDate
     *
     * @param \DateTime $serialsGeneratedDate
     *
     * @return Paint
     */
    public function setSerialsGeneratedDate($serialsGeneratedDate)
    {
        $this->serialsGeneratedDate = $serialsGeneratedDate;

        return $this;
    }

    /**
     * Get serialsGeneratedDate
     *
     * @return \DateTime
     */
    public function getSerialsGeneratedDate()
    {
        return $this->serialsGeneratedDate;
    }


    /**
     * Set color1
     *
     * @param string $color1
     *
     * @return Paint
     */
    public function setColor1($color1)
    {
        $this->color1 = $color1;

        return $this;
    }

    /**
     * Get color1
     *
     * @return string
     */
    public function getColor1()
    {
        return $this->color1;
    }

    /**
     * Set batch1EstimatedDeliveryDate
     *
     * @param \DateTime $batch1EstimatedDeliveryDate
     *
     * @return Paint
     */
    public function setBatch1EstimatedDeliveryDate($batch1EstimatedDeliveryDate)
    {
        $this->batch1EstimatedDeliveryDate = $batch1EstimatedDeliveryDate;

        return $this;
    }

    /**
     * Get batch1EstimatedDeliveryDate
     *
     * @return \DateTime
     */
    public function getBatch1EstimatedDeliveryDate()
    {
        return $this->batch1EstimatedDeliveryDate;
    }

    /**
     * Set color2
     *
     * @param string $color2
     *
     * @return Paint
     */
    public function setColor2($color2)
    {
        $this->color2 = $color2;

        return $this;
    }

    /**
     * Get color2
     *
     * @return string
     */
    public function getColor2()
    {
        return $this->color2;
    }

    /**
     * Set batch2EstimatedDeliveryDate
     *
     * @param \DateTime $batch2EstimatedDeliveryDate
     *
     * @return Paint
     */
    public function setBatch2EstimatedDeliveryDate($batch2EstimatedDeliveryDate)
    {
        $this->batch2EstimatedDeliveryDate = $batch2EstimatedDeliveryDate;

        return $this;
    }

    /**
     * Get batch2EstimatedDeliveryDate
     *
     * @return \DateTime
     */
    public function getBatch2EstimatedDeliveryDate()
    {
        return $this->batch2EstimatedDeliveryDate;
    }

    /**
     * Set batch1
     *
     * @param \V2\MainBundle\Entity\Batch $batch1
     *
     * @return Paint
     */
    public function setBatch1(\V2\MainBundle\Entity\Batch $batch1 = null)
    {
        $this->batch1 = $batch1;

        return $this;
    }

    /**
     * Get batch1
     *
     * @return \V2\MainBundle\Entity\Batch
     */
    public function getBatch1()
    {
        return $this->batch1;
    }

    /**
     * Set batch2
     *
     * @param \V2\MainBundle\Entity\Batch $batch2
     *
     * @return Paint
     */
    public function setBatch2(\V2\MainBundle\Entity\Batch $batch2 = null)
    {
        $this->batch2 = $batch2;

        return $this;
    }

    /**
     * Get batch2
     *
     * @return \V2\MainBundle\Entity\Batch
     */
    public function getBatch2()
    {
        return $this->batch2;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return Paint
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

}

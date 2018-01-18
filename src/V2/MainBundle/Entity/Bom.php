<?php

namespace V2\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bom
 *
 * @ORM\Table(name="bom")
 * @ORM\Entity(repositoryClass="V2\MainBundle\Repository\BomRepository")
 */
class Bom
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
     * @ORM\Column(name="issued_date", type="datetime", nullable=true)
     */
    private $issuedDate;

    /**
     * @ORM\ManyToOne(targetEntity="V2\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="issued_by", referencedColumnName="user_id")
     */
    private $issuedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="serials_generated_date", type="datetime", nullable=true)
     */
    private $serialsGeneratedDate;

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
     * @return Bom
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
     * @return Bom
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
     * @return Bom
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
     * @return Bom
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

}

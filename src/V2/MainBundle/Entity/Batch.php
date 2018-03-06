<?php

namespace V2\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Batch
 *
 * @ORM\Table(name="batch")
 * @ORM\Entity(repositoryClass="V2\MainBundle\Repository\BatchRepository")
 */
class Batch
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
     * @ORM\ManyToOne(targetEntity="V2\MainBundle\Entity\Paint")
     * @ORM\JoinColumn(name="paint_id", referencedColumnName="id")
     */
    private $paint;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=255)
     */
    private $color;

    /**
     * @var string
     *
     * @ORM\Column(name="ral_color", type="string", length=255)
     */
    private $ralColor;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor", type="string", length=255, nullable=true)
     */
    private $vendor;

    /**
     * @var string
     *
     * @ORM\Column(name="v2_po_number", type="string", length=255, nullable=true)
     */
    private $v2PoNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="kit_date", type="datetime", nullable=true)
     */
    private $kitDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="estimated_release_date", type="datetime", nullable=true)
     */
    private $estimatedReleaseDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="estimated_delivery_date", type="datetime", nullable=true)
     */
    private $estimatedDeliveryDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="received_date", type="datetime", nullable=true)
     */
    private $receivedDate;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return Batch
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set ralColor
     *
     * @param string $ralColor
     *
     * @return Batch
     */
    public function setRalColor($ralColor)
    {
        $this->ralColor = $ralColor;

        return $this;
    }

    /**
     * Get ralColor
     *
     * @return string
     */
    public function getRalColor()
    {
        return $this->ralColor;
    }

    /**
     * Set vendor
     *
     * @param string $vendor
     *
     * @return Batch
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * Get vendor
     *
     * @return string
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Set v2PoNumber
     *
     * @param string $v2PoNumber
     *
     * @return Batch
     */
    public function setV2PoNumber($v2PoNumber)
    {
        $this->v2PoNumber = $v2PoNumber;

        return $this;
    }

    /**
     * Get v2PoNumber
     *
     * @return string
     */
    public function getV2PoNumber()
    {
        return $this->v2PoNumber;
    }

    /**
     * Set kitDate
     *
     * @param \DateTime $kitDate
     *
     * @return Batch
     */
    public function setKitDate($kitDate)
    {
        $this->kitDate = $kitDate;

        return $this;
    }

    /**
     * Get kitDate
     *
     * @return \DateTime
     */
    public function getKitDate()
    {
        return $this->kitDate;
    }

    /**
     * Set estimatedReleaseDate
     *
     * @param \DateTime $estimatedReleaseDate
     *
     * @return Batch
     */
    public function setEstimatedReleaseDate($estimatedReleaseDate)
    {
        $this->estimatedReleaseDate = $estimatedReleaseDate;

        return $this;
    }

    /**
     * Get estimatedReleaseDate
     *
     * @return \DateTime
     */
    public function getEstimatedReleaseDate()
    {
        return $this->estimatedReleaseDate;
    }

    /**
     * Set estimatedDeliveryDate
     *
     * @param \DateTime $estimatedDeliveryDate
     *
     * @return Batch
     */
    public function setEstimatedDeliveryDate($estimatedDeliveryDate)
    {
        $this->estimatedDeliveryDate = $estimatedDeliveryDate;

        return $this;
    }

    /**
     * Get estimatedDeliveryDate
     *
     * @return \DateTime
     */
    public function getEstimatedDeliveryDate()
    {
        return $this->estimatedDeliveryDate;
    }

    /**
     * Set receivedDate
     *
     * @param \DateTime $receivedDate
     *
     * @return Batch
     */
    public function setReceivedDate($receivedDate)
    {
        $this->receivedDate = $receivedDate;

        return $this;
    }

    /**
     * Get receivedDate
     *
     * @return \DateTime
     */
    public function getReceivedDate()
    {
        return $this->receivedDate;
    }

    /**
     * Set notes
     *
     * @param string $notes
     *
     * @return Batch
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
     * @return Batch
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
     * Set paint
     *
     * @param \V2\MainBundle\Entity\Paint $paint
     *
     * @return Batch
     */
    public function setPaint(\V2\MainBundle\Entity\Paint $paint = null)
    {
        $this->paint = $paint;

        return $this;
    }

    /**
     * Get paint
     *
     * @return \V2\MainBundle\Entity\Paint
     */
    public function getPaint()
    {
        return $this->paint;
    }

    /**
     * Set updatedBy
     *
     * @param \V2\UserBundle\Entity\User $updatedBy
     *
     * @return Batch
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

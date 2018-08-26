<?php

namespace V2\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * KittingShort
 *
 * @ORM\Table(name="kitting_short")
 * @ORM\Entity(repositoryClass="V2\MainBundle\Repository\KittingShortRepository")
 */
class KittingShort
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
     * @ORM\ManyToOne(targetEntity="V2\MainBundle\Entity\Kitting")
     * @ORM\JoinColumn(name="kitting_id", referencedColumnName="id")
     */
    private $kitting;

    /**
     * @var string
     *
     * @ORM\Column(name="part_number", type="string", length=255)
     */
    private $partNumber;

    /**
     * 0  = empty
     * 1  = painted
     * 2  = ignore
     *
     * @var int
     *
     * @ORM\Column(name="short_class", type="integer", length=255)
     */
    private $shortClass;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_needed", type="datetime", nullable=true)
     */
    private $dateNeeded;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor", type="string", length=255, nullable=true)
     */
    private $vendor;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor_po_number", type="string", length=255, nullable=true)
     */
    private $vendorPoNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="mod_wo", type="string", length=255, nullable=true)
     */
    private $modWo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="estimated_delivery_date", type="datetime", nullable=true)
     */
    private $estimatedDeliveryDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="mod_done_date", type="datetime", nullable=true)
     */
    private $modDoneDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="received_date", type="datetime", nullable=true)
     */
    private $receivedDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="parts_pulled_date", type="datetime", nullable=true)
     */
    private $partsPulledDate;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="string", length=255, nullable=true)
     */
    private $notes;

    /**
     * This quantity here has no relation to any jobs
     *
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;


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
        $this->quantity = 0;
        $this->shortClass = 0;
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
     * Set partNumber
     *
     * @param string $partNumber
     *
     * @return KittingShort
     */
    public function setPartNumber($partNumber)
    {
        $this->partNumber = $partNumber;

        return $this;
    }

    /**
     * Get partNumber
     *
     * @return string
     */
    public function getPartNumber()
    {
        return $this->partNumber;
    }

    /**
     * Set shortClass
     *
     * @param boolean $shortClass
     *
     * @return KittingShort
     */
    public function setShortClass($shortClass)
    {
        $this->shortClass = $shortClass;

        return $this;
    }

    /**
     * Get shortClass
     *
     * @return int
     */
    public function getShortClass()
    {
        return $this->shortClass;
    }

    /**
     * Set dateNeeded
     *
     * @param \DateTime $dateNeeded
     *
     * @return KittingShort
     */
    public function setDateNeeded($dateNeeded)
    {
        $this->dateNeeded = $dateNeeded;

        return $this;
    }

    /**
     * Get dateNeeded
     *
     * @return \DateTime
     */
    public function getDateNeeded()
    {
        return $this->dateNeeded;
    }

    /**
     * Set vendor
     *
     * @param string $vendor
     *
     * @return KittingShort
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
     * Set vendorPoNumber
     *
     * @param string $vendorPoNumber
     *
     * @return KittingShort
     */
    public function setVendorPoNumber($vendorPoNumber)
    {
        $this->vendorPoNumber = $vendorPoNumber;

        return $this;
    }

    /**
     * Get vendorPoNumber
     *
     * @return string
     */
    public function getVendorPoNumber()
    {
        return $this->vendorPoNumber;
    }

    /**
     * Set modWo
     *
     * @param string $modWo
     *
     * @return KittingShort
     */
    public function setModWo($modWo)
    {
        $this->modWo = $modWo;

        return $this;
    }

    /**
     * Get modWo
     *
     * @return string
     */
    public function getModWo()
    {
        return $this->modWo;
    }

    /**
     * Set estimatedDeliveryDate
     *
     * @param \DateTime $estimatedDeliveryDate
     *
     * @return KittingShort
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
     * Set modDoneDate
     *
     * @param \DateTime $modDoneDate
     *
     * @return KittingShort
     */
    public function setModDoneDate($modDoneDate)
    {
        $this->modDoneDate = $modDoneDate;

        return $this;
    }

    /**
     * Get modDoneDate
     *
     * @return \DateTime
     */
    public function getModDoneDate()
    {
        return $this->modDoneDate;
    }

    /**
     * Set receivedDate
     *
     * @param \DateTime $receivedDate
     *
     * @return KittingShort
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
     * Set partsPulledDate
     *
     * @param \DateTime $partsPulledDate
     *
     * @return KittingShort
     */
    public function setPartsPulledDate($partsPulledDate)
    {
        $this->partsPulledDate = $partsPulledDate;

        return $this;
    }

    /**
     * Get partsPulledDate
     *
     * @return \DateTime
     */
    public function getPartsPulledDate()
    {
        return $this->partsPulledDate;
    }

    /**
     * Set notes
     *
     * @param string $notes
     *
     * @return KittingShort
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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return KittingShort
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set kitting
     *
     * @param \V2\MainBundle\Entity\Kitting $kitting
     *
     * @return KittingShort
     */
    public function setKitting(\V2\MainBundle\Entity\Kitting $kitting = null)
    {
        $this->kitting = $kitting;

        return $this;
    }

    /**
     * Get kitting
     *
     * @return \V2\MainBundle\Entity\Kitting
     */
    public function getKitting()
    {
        return $this->kitting;
    }

    /**
     * Set updateTime
     *
     * @param \DateTime $updateTime
     *
     * @return KittingShort
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
     * @return KittingShort
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


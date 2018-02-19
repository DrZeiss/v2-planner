<?php

namespace V2\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Job
 *
 * @ORM\Table(name="job")
 * @ORM\Entity(repositoryClass="V2\MainBundle\Repository\JobRepository")
 */
class Job
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="sales_order", type="integer")
     */
    private $salesOrder;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="estimated_ship_date", type="datetime")
     */
    private $estimatedShipDate;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="model", type="string", length=255)
     */
    private $model;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="manufacturing_order", type="string", length=255, nullable=true)
     */
    private $manufacturingOrder;

    /**
     * @var string
     *
     * @ORM\Column(name="mac_purchase_order", type="string", length=255, nullable=true)
     */
    private $macPurchaseOrder;

    /**
     * @ORM\ManyToOne(targetEntity="V2\MainBundle\Entity\BuildLocation")
     * @ORM\JoinColumn(name="build_location", referencedColumnName="id")
     */
    private $buildLocation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="planner_estimated_ship_date", type="datetime", nullable=true)
     */
    private $plannerEstimatedShipDate;

    /**
     * @ORM\OneToOne(targetEntity="V2\MainBundle\Entity\Bom", mappedBy="job")
     */
    private $bom;

    /**
     * @ORM\OneToOne(targetEntity="V2\MainBundle\Entity\Shipping", mappedBy="job")
     */
    private $shipping;

    /**
     * @ORM\OneToOne(targetEntity="V2\MainBundle\Entity\Kitting", mappedBy="job")
     */
    private $kitting;

    /**
     * @ORM\OneToOne(targetEntity="V2\MainBundle\Entity\Scheduling", mappedBy="job")
     */
    private $scheduling;

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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Job
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set salesOrder
     *
     * @param integer $salesOrder
     *
     * @return Job
     */
    public function setSalesOrder($salesOrder)
    {
        $this->salesOrder = $salesOrder;

        return $this;
    }

    /**
     * Get salesOrder
     *
     * @return int
     */
    public function getSalesOrder()
    {
        return $this->salesOrder;
    }

    /**
     * Set estimatedShipDate
     *
     * @param \DateTime $estimatedShipDate
     *
     * @return Job
     */
    public function setEstimatedShipDate($estimatedShipDate)
    {
        $this->estimatedShipDate = $estimatedShipDate;

        return $this;
    }

    /**
     * Get estimatedShipDate
     *
     * @return \DateTime
     */
    public function getEstimatedShipDate()
    {
        return $this->estimatedShipDate;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Job
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set model
     *
     * @param string $model
     *
     * @return Job
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Job
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
     * Set manufacturingOrder
     *
     * @param string $manufacturingOrder
     *
     * @return Job
     */
    public function setManufacturingOrder($manufacturingOrder)
    {
        $this->manufacturingOrder = $manufacturingOrder;

        return $this;
    }

    /**
     * Get manufacturingOrder
     *
     * @return string
     */
    public function getManufacturingOrder()
    {
        return $this->manufacturingOrder;
    }

    /**
     * Set macPurchaseOrder
     *
     * @param string $macPurchaseOrder
     *
     * @return Job
     */
    public function setMacPurchaseOrder($macPurchaseOrder)
    {
        $this->macPurchaseOrder = $macPurchaseOrder;

        return $this;
    }

    /**
     * Get macPurchaseOrder
     *
     * @return string
     */
    public function getMacPurchaseOrder()
    {
        return $this->macPurchaseOrder;
    }

    /**
     * Set buildLocation
     *
     * @param integer $buildLocation
     *
     * @return Job
     */
    public function setBuildLocation($buildLocation)
    {
        $this->buildLocation = $buildLocation;

        return $this;
    }

    /**
     * Get buildLocation
     *
     * @return int
     */
    public function getBuildLocation()
    {
        return $this->buildLocation;
    }

    /**
     * Set plannerEstimatedShipDate
     *
     * @param \DateTime $plannerEstimatedShipDate
     *
     * @return Job
     */
    public function setPlannerEstimatedShipDate($plannerEstimatedShipDate)
    {
        $this->plannerEstimatedShipDate = $plannerEstimatedShipDate;

        return $this;
    }

    /**
     * Get plannerEstimatedShipDate
     *
     * @return \DateTime
     */
    public function getPlannerEstimatedShipDate()
    {
        return $this->plannerEstimatedShipDate;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     *
     * @return Job
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
     * Set updateTime
     *
     * @param \DateTime $updateTime
     *
     * @return Job
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
     * Set createdBy
     *
     * @param \V2\UserBundle\Entity\User $createdBy
     *
     * @return Job
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
     * Set updatedBy
     *
     * @param \V2\UserBundle\Entity\User $updatedBy
     *
     * @return Job
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
     * Set bom
     *
     * @param \V2\MainBundle\Entity\Bom $bom
     *
     * @return Job
     */
    public function setBom(\V2\MainBundle\Entity\Bom $bom = null)
    {
        $this->bom = $bom;

        return $this;
    }

    /**
     * Get bom
     *
     * @return \V2\MainBundle\Entity\Bom
     */
    public function getBom()
    {
        return $this->bom;
    }

    /**
     * Set kitting
     *
     * @param \V2\MainBundle\Entity\Kitting $kitting
     *
     * @return Job
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
     * Set shipping
     *
     * @param \V2\MainBundle\Entity\Shipping $shipping
     *
     * @return Job
     */
    public function setShipping(\V2\MainBundle\Entity\Shipping $shipping = null)
    {
        $this->shipping = $shipping;

        return $this;
    }

    /**
     * Get shipping
     *
     * @return \V2\MainBundle\Entity\Shipping
     */
    public function getShipping()
    {
        return $this->shipping;
    }

    /**
     * Set scheduling
     *
     * @param \V2\MainBundle\Entity\Scheduling $scheduling
     *
     * @return Job
     */
    public function setScheduling(\V2\MainBundle\Entity\Scheduling $scheduling = null)
    {
        $this->scheduling = $scheduling;

        return $this;
    }

    /**
     * Get scheduling
     *
     * @return \V2\MainBundle\Entity\Scheduling
     */
    public function getScheduling()
    {
        return $this->scheduling;
    }
}

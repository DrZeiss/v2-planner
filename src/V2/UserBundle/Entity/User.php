<?php

namespace V2\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User
 *
 * @ORM\Table(name="v2_user")
 * @ORM\Entity(repositoryClass="V2\UserBundle\Repository\UserRepository")
 * @UniqueEntity("email")
 */
class User extends BaseUser
{
    // General roles
    const ROLE_SUPER_ADMIN      = 'SUPER ADMIN';
    const ROLE_ADMIN            = 'ADMIN';
    const ROLE_DEFAULT          = 'ROLE_USER';
    const ROLE_USER             = 'ROLE_USER';    
    // Specific roles
    const ROLE_BOM_BUILDER      = 'BOM_BUILDER';
    const ROLE_KITTER           = 'KITTER';
    const ROLE_RECEIVER         = 'RECEIVER';
    const ROLE_MANUFACTURER     = 'MANUFACTURER';
    const ROLE_SUPPLY_CHAIN     = 'SUPPLY CHAIN';
    const ROLE_MAC_PRODUCTION   = 'MAC PRODUCTION';
    const ROLE_V2_PRODUCTION    = 'V2 PRODUCTION';
    const ROLE_SCHEDULER        = 'SCHEDULER';
    const ROLE_SHIPPER          = 'SHIPPER';
    const ROLE_PAINTER          = 'PAINTER';

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="fullname", type="string", length=255, nullable=true)
     */
    protected $fullname;

    /**
     * @ORM\Column(name="create_time", type="datetime", nullable=false)
     */
    protected $createTime;

    /**
     * @ORM\Column(name="update_time", type="datetime", nullable=false)
     */
    protected $updateTime;

    /**
     * @ORM\ManyToOne(targetEntity="V2\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="user_id")
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="V2\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="user_id")
     */
    private $updatedBy;

    public function __construct()
    {
        parent::__construct();
        $this->createTime           = new \DateTime();
        $this->updateTime           = new \DateTime();        
    }

    public function getInitials()
    {
        $words = explode(' ', $this->fullname);
        $initials = '';
        foreach ($words as $word) {
            $initials .= $word[0];
        }
        return strtoupper($initials);
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
     * Set fullname
     *
     * @param string $fullname
     *
     * @return User
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get fullname
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
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
     * @return User
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
     * @return User
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
     * @return User
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


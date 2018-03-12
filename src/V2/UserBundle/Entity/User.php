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
     * @ORM\Column(name="create_time", type="datetime", nullable=false)
     */
    protected $createTime;

    /**
     * @ORM\Column(name="update_time", type="datetime", nullable=false)
     */
    protected $updateTime;

    public function __construct()
    {
        parent::__construct();
        $this->createTime           = new \DateTime();
        $this->updateTime           = new \DateTime();        
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
}


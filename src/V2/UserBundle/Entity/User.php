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
    const ROLE_SUPER_ADMIN      = 'ROLE_SUPER_ADMIN';
    const ROLE_ADMIN            = 'ROLE_ADMIN';
    const ROLE_DEFAULT          = 'ROLE_USER';
    // Specific roles
    const ROLE_BOM_BUILDER      = 'ROLE_BOM_BUILDER';
    const ROLE_KITTER           = 'ROLE_KITTER';
    const ROLE_RECEIVER         = 'ROLE_RECEIVER';
    const ROLE_MANUFACTURER     = 'ROLE_MANUFACTURER';
    const ROLE_SUPPLY_CHAIN     = 'ROLE_SUPPLY_CHAIN';
    const ROLE_MAC_PRODUCTION   = 'ROLE_MAC_PRODUCTION';
    const ROLE_V2_PRODUCTION    = 'ROLE_V2_PRODUCTION';
    const ROLE_SCHEDULER        = 'ROLE_SCHEDULER';

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


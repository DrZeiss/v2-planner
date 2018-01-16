<?php

namespace V2\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/users")
 */
class UserController extends Controller
{
    /**
     * @Route(name="users")
     */
    public function indexAction()
    {
        return $this->render('V2UserBundle:Default:index.html.twig');
    }
}

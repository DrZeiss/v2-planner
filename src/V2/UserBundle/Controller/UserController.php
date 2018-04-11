<?php

namespace V2\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use V2\UserBundle\Entity\User;
use V2\UserBundle\Form\UserType;
use V2\UserBundle\Form\EditUserType;

/**
 * @Route("/users")
 */
class UserController extends Controller
{
    protected $em;
    protected $jobRepository;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->userRepository = $this->em->getRepository('V2UserBundle:User');
    }

    /**
     * @Route(name="users")
     */
    public function indexAction(Request $request)
    {
        $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        // Check for permissions
        if (!($isAdmin)) {
            $this->addFlash('error', 'You have no permission!');
            return $this->redirect($this->generateUrl('dashboard'));
        }

        $defaultParameters = array(
            'name' => null,
        );
        $parameters = array_merge($defaultParameters, $request->query->all());

        $users = $this->userRepository->findUsers($parameters);

        return $this->render('users/index.html.twig', array(
            'users'     =>  $users,
            'name'      =>  $parameters['name'],
        ));
    }

    /**
     * @Route("/create", name="create_user")
     */
    public function createUser(Request $request)
    {
        $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        // Check for permissions
        if (!($isAdmin)) {
            $this->addFlash('error', 'You have no permission!');
            return $this->redirect($this->generateUrl('dashboard'));
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $user->setEnabled(true);
                    $user->setCreatedBy($this->getUser());
                    $user->setCreateTime(new \DateTime());
                    $user->setUpdatedBy($this->getUser());
                    $user->setUpdateTime(new \DateTime());
                    $this->em->persist($user);
                    $this->em->flush();
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error while creating new user! '.$e->getMessage());
                    return $this->redirect($request->getUri());
                }
                $this->addFlash('success', 'User created!');
                return $this->redirect($this->generateUrl('users'));
            } else {
                $this->addFlash('error', 'Error while creating new user! '.$form->getErrors());
                return $this->redirect($request->getUri());                
            }

        }

        return $this->render('users/create.html.twig', array(
            'user'  =>  $user,
            'form'  =>  $form->createView(),
        ));
    }

    /**
     * @Route("/{userId}/edit", name="edit_user")
     */
    public function editUser(Request $request, $userId)
    {
        $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        $isMe = $this->getUser()->getId() == $userId ? true : false;
        $user = $this->userRepository->find($userId);
        
        // Check for permissions
        if (!($isAdmin || $isMe)) {
            $this->addFlash('error', 'You have no permission!');
            return $this->redirect($this->generateUrl('dashboard'));
        }

        $form = $this->createForm(EditUserType::class, $user, array(
            'isMe' => $isMe,
            'isAdmin' => $isAdmin,
        ));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $user->setUpdatedBy($this->getUser());
                    $user->setUpdateTime(new \DateTime());
                    $this->em->persist($user);
                    $this->em->flush();
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error while updating user! '.$e->getMessage());
                    return $this->redirect($request->getUri());
                }
                $this->addFlash('success', 'User updated!');
                if ($isAdmin) {
                    return $this->redirect($this->generateUrl('users'));
                } else {
                    return $this->redirect($this->generateUrl('dashboard'));   
                }
            }
        }

        return $this->render('users/edit.html.twig', array(
            'user'  =>  $user,
            'form'  =>  $form->createView(),
        ));
    }

}

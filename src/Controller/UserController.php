<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/users")
 */
class UserController extends AbstractFOSRestController
{
    /**
     * @Get(name="api_users_list")
     * @View
     */
    public function listAction(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();
        return $users;
    }

    /**
     * @Get("/{id}", name="api_users_show_one", requirements= {"id"="\d+"})
     * @View
     */
    public function showAction(User $user)
    {
        return $user;
    }
}

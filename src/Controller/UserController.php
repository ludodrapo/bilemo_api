<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class UserController
 * @package App\Controller
 * @Rest\Route("/users")
 */
class UserController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(name="api_users_list")
     * @Rest\View(
     *     statusCode = 200
     * )
     */
    public function listAction(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();
        return $users;
    }

    /**
     * @Rest\Get("/{id}", name="api_users_show_one", requirements= {"id"="\d+"})
     * @Rest\View(
     *     statusCode = 200
     * )
     */
    public function showAction(User $user)
    {
        return $user;
    }

    /**
     * @Rest\Post(name="api_users_create_one")
     * @Rest\View(
     *     statusCode = 201
     * )
     * @ParamConverter(
     *     "user",
     *     converter="fos_rest.request_body"
     * )
     * @param User $user
     * @param EntityManagerInterface $em
     * @return void
     */
    public function createAction(
        User $user,
        EntityManagerInterface $em,
        ClientRepository $clientRepository
    ) {
        $em->persist(
            $user
                ->setClient($clientRepository->findOneBy([]))
                ->setCreatedAt(new \DateTimeImmutable())
        );
        $em->flush();

        return $user;
    }

    /**
     * @Rest\Delete("/{id}", name="api_users_delete_one")
     * @Rest\View(
     *     statusCode = 204
     * )
     * @param User $user
     * @param EntityManagerInterface $em
     * @return void
     */
    public function deleteAction(User $user, EntityManagerInterface $em)
    {
        $em->remove($user);
        $em->flush();

        return;
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
     * @param UserRepository $userRepository
     * @return Response
     */
    public function listAction(UserRepository $userRepository)
    {
        $client = $this->getUser();
        $users = $userRepository->findBy(
            [
                'client' => $client->getId()
            ]
        );

        return $users;
    }

    /**
     * @Rest\Get(
     *     "/{id}", 
     *     name="api_users_show_one", 
     *     requirements= {"id"="\d+"}
     * )
     * @Rest\View(
     *     statusCode = 200
     * )
     * @return Response
     */
    public function showAction(User $user)
    {
        if ($user->getClient() !== $this->getUser()) {
            return $this->view(
                'You cannot access a user from another organization',
                Response::HTTP_FORBIDDEN
            );
        }
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
     * @param ConstraintViolationList $violations
     * @return Response
     */
    public function createAction(
        User $user,
        EntityManagerInterface $em,
        ClientRepository $clientRepository,
        ConstraintViolationList $violations
    ) {

        if (count($violations)) {
            $data = ['The sent JSON contains invalid data:'];
            foreach ($violations as $violation) {
                $violationData = sprintf(
                    "Field '%s': '%s'",
                    $violation->getPropertyPath(),
                    $violation->getMessage()
                );
                $data[] = $violationData;
            }
            return $this->view($data, Response::HTTP_BAD_REQUEST);
        }

        $em->persist(
            $user
                ->setClient($this->getUser())
                ->setCreatedAt(new \DateTimeImmutable())
        );
        $em->flush();

        return $this->view(
            $user,
            Response::HTTP_CREATED,
            [
                'location' => $this->generateUrl(
                    'api_users_show_one',
                    ['id' => $user->getId()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            ]
        );
    }

    /**
     * @Rest\Delete("/{id}", name="api_users_delete_one")
     * @Rest\View(
     *     statusCode = 204
     * )
     * @param User $user
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function deleteAction(User $user, EntityManagerInterface $em)
    {
        if ($user->getClient() !== $this->getUser()) {
            return $this->view(
                'You cannot delete a user from another organization',
                Response::HTTP_FORBIDDEN
            );
        }
        $em->remove($user);
        $em->flush();

        return;
    }
}

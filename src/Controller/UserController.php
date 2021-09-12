<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Representation\UsersListPaginator;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

/**
 * Class UserController
 * @package App\Controller
 * @Rest\Route("/users")
 */
class UserController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(name="api_users_list")
     * 
     * @Rest\QueryParam(
     *     name="page",
     *     requirements="\d+",
     *     default=1,
     *     description="Page where to start"
     * )
     * 
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default=5,
     *     description="Number of users per page"
     * )
     * 
     * @Rest\View(
     *     statusCode=200
     * )
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns a paginated users list.",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(
     *             ref=@Model(type=User::class)
     *         )
     *     )
     * )
     * 
     * @param ClientRepository $clientRepository
     * @param ParamFetcherInterface $paramFetcher
     * @param UsersListPaginator $paginator
     */
    public function listAction(
        ClientRepository $clientRepository,
        ParamFetcherInterface $paramFetcher,
        UsersListPaginator $paginator
    ) {
        $client = $clientRepository->find(
            $this->getUser()->getId()
        );
        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('limit');

        $paginatedUsersList = $paginator->getPaginatedList($client, $page, $limit);
        return $paginatedUsersList;
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
     * @OA\Response(
     *     response=200,
     *     description="Returns one user's details.",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(
     *             ref=@Model(
     *                 type=User::class
     *             )
     *         )
     *     )
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

        dd($user);

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

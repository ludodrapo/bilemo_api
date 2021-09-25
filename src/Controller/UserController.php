<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\ForbiddenAccessException;
use App\Exception\ResourceValidationException;
use App\Manager\UserManager;
use OpenApi\Annotations as OA;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Representation\UsersListPaginator;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
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
     * @OA\Get(
     *     tags={"Users"},
     *     summary="Returns a paginated list of users"
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns a paginated users list.",
     *     @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(
     *             @OA\Property(
     *                 property="page",
     *                 type="integer",
     *                 example="1"
     *             ),
     *             @OA\Property(
     *                 property="limit",
     *                 type="integer",
     *                 example="5"
     *             ),
     *             @OA\Property(
     *                 property="pages",
     *                 type="integer",
     *                 example="11"
     *             ),
     *             @OA\Property(
     *                 property="total",
     *                 type="integer",
     *                 example="54"
     *             ),
     *             @OA\Property(
     *                 property="_links",
     *                 type="object",
     *                 @OA\Property(
     *                     property="self",
     *                     type="object",
     *                     @OA\Property(
     *                         property="href",
     *                         type="string",
     *                         example="https://localhost:8000/api/users?page=1&limit=5"
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="first",
     *                     type="object",
     *                     @OA\Property(
     *                         property="href",
     *                         type="string",
     *                         example="https://localhost:8000/api/users?page=1&limit=5"
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="last",
     *                     type="object",
     *                     @OA\Property(
     *                         property="href",
     *                         type="string",
     *                         example="https://localhost:8000/api/users?page=1&limit=5"
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="next",
     *                     type="object",
     *                     @OA\Property(
     *                         property="href",
     *                         type="string",
     *                         example="https://localhost:8000/api/users?page=1&limit=5"
     *                     )
     *                 ),
     *             ),
     *             @OA\Property(
     *                 property="_embedded",
     *                 type="object",
     *                 @OA\Property(
     *                     property="items",
     *                     type="array",
     *                     @OA\Items(
     *                         ref=@Model(
     *                             type=User::class
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * @param ClientRepository $clientRepository
     * @param ParamFetcherInterface $paramFetcher
     * @param UsersListPaginator $paginator
     * @param TagAwareCacheInterface $cacheInterface
     */
    public function listAction(
        ClientRepository $clientRepository,
        ParamFetcherInterface $paramFetcher,
        UsersListPaginator $paginator,
        TagAwareCacheInterface $cacheInterface
    ) {
        $client = $clientRepository->find(
            $this->getUser()->getId()
        );
        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('limit');

        $paginatedUsersList = $cacheInterface->get(
            'client_' . $client->getId() . '_users_list_page_' . $page . '_limit_' . $limit,
            function (ItemInterface $itemInterface) use ($paginator, $client, $page, $limit) {
                $itemInterface
                    ->tag('users')
                    ->expiresAfter(31536000);
                return $paginator->getPaginatedList($client, $page, $limit);
            }
        );

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
     * @OA\Get(
     *     tags={"Users"},
     *     summary="Returns one user's details"
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Resource's ID",
     *     required=true,
     *     @OA\Schema(type="integer")
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns one user's details.",
     *     @OA\JsonContent(
     *         ref=@Model(
     *             type=User::class
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Occurs when the user's id does not exist."
     * )
     * @OA\Response(
     *     response=403,
     *     description="Occurs when trying to access a user that is not associated with the current logged in client."
     * )
     * @param User $user
     * @param TagAwareCacheInterface $cacheInterface
     * @return Response
     */
    public function showAction(User $user, TagAwareCacheInterface $cacheInterface)
    {
        if ($user->getClient() !== $this->getUser()) {
            throw new ForbiddenAccessException("You cannot access another organization's user's details", 403);
        }

        $userToDisplay = $cacheInterface->get(
            'user' . $user->getId(),
            function (ItemInterface $itemInterface) use ($user) {
                $itemInterface->expiresAfter(31536000);
                return $user;
            }
        );

        return $userToDisplay;
    }

    /**
     * @Rest\Post(name="api_users_create_one")
     * @ParamConverter(
     *     "user",
     *     converter="fos_rest.request_body"
     * )
     * @OA\Post(
     *     tags={"Users"},
     *     summary="To create a new user"
     * )
     * @OA\RequestBody(
     *     @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(
     *             @OA\Property(
     *                 property="name",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string"
     *             ),
     *             example={"name": "Jessica Smith", "email": "jessica.smith@gmail.com"}
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=204,
     *     description="User created and stored in the database."
     * )
     * 
     * @param User $user
     * @param EntityManagerInterface $em
     * @param ConstraintViolationList $violations
     * @return Response
     */
    public function createAction(
        User $user,
        UserManager $um,
        ConstraintViolationList $violations,
        TagAwareCacheInterface $cacheInterface
    ) {
        if (count($violations)) {
            $message = 'The sent JSON contains invalid data:';
            foreach ($violations as $violation) {
                $message .= sprintf(
                    " Field '%s': %s",
                    $violation->getPropertyPath(),
                    $violation->getMessage()
                );
            }

            throw new ResourceValidationException($message, 400);
        }

        $client = $this->getUser();
        $um->recordUser($user, $client);

        //To clear users list cache when a new one is recorded
        $cacheInterface->invalidateTags(['users']);

        return $this->view(
            null,
            Response::HTTP_NO_CONTENT,
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
     * @OA\Delete(
     *     tags={"Users"},
     *     summary="To delete a user"
     * )
     * @OA\Response(
     *     response=204,
     *     description="When user's deletion succeeded."
     * )
     * @OA\Response(
     *     response=403,
     *     description="Occurs when trying to delete a user that is not associated with the current logged in client.",
     * )
     * @OA\Response(
     *     response=404,
     *     description="Occurs when trying to delete a user that does not exist.",
     * )
     * @param User $user
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function deleteAction(
        User $user,
        UserManager $um,
        TagAwareCacheInterface $cacheInterface
    ) {
        if ($user->getClient() !== $this->getUser()) {
            throw new ForbiddenAccessException('You cannot delete a user from another organization', 403);
        }

        $um->deleteUser($user);
        //To clear users list cache when a new one is deleted
        $cacheInterface->invalidateTags(['users']);

        return;
    }
}

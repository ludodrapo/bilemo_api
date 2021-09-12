<?php

namespace App\Controller;

use App\Entity\Item;
use App\Repository\ItemRepository;
use JMS\Serializer\SerializerInterface;
use App\Representation\ItemsListPaginator;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

/**
 * Class ItemController
 * @package App\Controller
 * @Rest\Route("/items")
 */
class ItemController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(name="api_items_list")
     * @Rest\QueryParam(
     *     name="page",
     *     requirements="\d+",
     *     default=1,
     *     description="Page where to start"
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default=10,
     *     description="Number of items per page"
     * )
     * @Rest\View(
     *     statusCode=200
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns a paginated items list.",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(
     *             ref=@Model(
     *                 type=Item::class,
     *                 groups={"list"}
     *             )
     *         )
     *     )
     * )
     */
    public function listAction(
        SerializerInterface $serializer,
        ParamFetcherInterface $paramFetcher,
        ItemsListPaginator $paginator
    ) {

        $paginatedItemsList = $paginator->getPaginatedList(
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $paginatedItemsList;

        // return new JsonResponse(
        //     $serializer->serialize(
        //         $paginatedItemsList,
        //         'json'
        //     ),
        //     JsonResponse::HTTP_OK,
        //     [],
        //     true
        // );
    }

    /**
     * @Rest\Get("/{id}", name="api_items_show_one")
     * @Rest\View(
     *     statusCode = 200,
     *     serializerGroups = {"show"}
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns an item's details.",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(
     *             ref=@Model(
     *                 type=Item::class,
     *                 groups={"show"}
     *             )
     *         )
     *     )
     * )
     */
    public function showAction(Item $item)
    {
        return $item;
    }
}

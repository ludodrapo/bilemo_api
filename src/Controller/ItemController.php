<?php

namespace App\Controller;

use App\Entity\Item;
use App\Repository\ItemRepository;
use App\Representation\ItemsListPaginator;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class ItemController
 * @package App\Controller
 * @Rest\Route("/items")
 */
class ItemController extends AbstractFOSRestController
{
    // /**
    //  * @var ParamFetcherInterface
    //  */
    // private $paramFetcher;

    // /**
    //  * @var ItemsListPaginator
    //  */
    // private $paginator;

    // public function __construct(
    //     ParamFetcherInterface $paramFetcher,
    //     ItemsListPaginator $paginator
    // ) {
    //     $this->paramFetcher = $paramFetcher;
    //     $this->paginator = $paginator;
    // }

    /**
     * @Rest\Get(name="api_items_list")
     * 
     * @Rest\View(
     *     statusCode = 200,
     *     serializerGroups={"Default", "list"}
     * )
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
     *     default=10,
     *     description="Number of items per page"
     * )
     * @OA\Get(
     *     tags={"Items"},
     *     summary="Returns a paginated list of items"
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns a paginated items list.",
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
     *                 example="10"
     *             ),
     *             @OA\Property(
     *                 property="pages",
     *                 type="integer",
     *                 example="4"
     *             ),
     *             @OA\Property(
     *                 property="total",
     *                 type="integer",
     *                 example="37"
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
     *                         example="https://localhost:8000/api/items?page=1&limit=10"
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="first",
     *                     type="object",
     *                     @OA\Property(
     *                         property="href",
     *                         type="string",
     *                         example="https://localhost:8000/api/items?page=1&limit=10"
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="last",
     *                     type="object",
     *                     @OA\Property(
     *                         property="href",
     *                         type="string",
     *                         example="https://localhost:8000/api/items?page=4&limit=10"
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="next",
     *                     type="object",
     *                     @OA\Property(
     *                         property="href",
     *                         type="string",
     *                         example="https://localhost:8000/api/items?page=2&limit=10"
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
     *                             type=Item::class,
     *                             groups={"list"}
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function listAction(
        ParamFetcherInterface $paramFetcher,
        ItemsListPaginator $paginator,
        CacheInterface $cache
    ) {

        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('limit');

        $paginatedItemsList = $cache->get(
            'items_list_page_' . $page . '_limit_' . $limit,
            function (ItemInterface $itemInterface) use ($paginator, $page, $limit) {
                $itemInterface->expiresAfter(31536000);
                return $paginator->getPaginatedList($page, $limit);
            }
        );

        return $paginatedItemsList;
    }

    /**
     * @Rest\Get("/{id}", name="api_items_show_one")
     * 
     * @Rest\View(
     *     statusCode = 200,
     *     serializerGroups = {"show"}
     * )
     * @OA\Get(
     *     tags={"Items"},
     *     summary="Returns a item's details"
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
     *     description="Returns an item's details.",
     *     @OA\JsonContent(
     *         ref=@Model(
     *             type=Item::class,
     *             groups={"show"}
     *         )
     *     )
     * )
     * @OA\Response(
     *     response="404",
     *     description="Occurs when the item's id does not exist.",
     *     @OA\MediaType(
     *         mediaType="text/html",
     *         example="The resource(s) you asked for do(es) not exist (at least not anymore)."
     *     )
     * )
     */
    public function showAction(Item $item, CacheInterface $cache)
    {
        $itemToDisplay = $cache->get(
            'item' . $item->getId(),
            function (ItemInterface $itemInterface) use ($item) {
                $itemInterface->expiresAfter(31536000);
                return $item;
            }
        );

        return $itemToDisplay;
    }
}

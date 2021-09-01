<?php

namespace App\Controller;

use App\Entity\Item;
use App\Repository\ItemRepository;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;

/**
 * Class ItemController
 * @package App\Controller
 * @Route("/items")
 */
class ItemController extends AbstractFOSRestController
{
    /**
     * @Get(name="api_items_list")
     * @View(
     *     statusCode = 200,
     *     serializerGroups = {"list"}
     * )
     */
    public function listAction(ItemRepository $itemRepository)
    {
        $items = $itemRepository->findAll();
        return $items;
    }

    /**
     * @Get("/{id}", name="api_items_show_one")
     * @View(
     *     statusCode = 200,
     *     serializerGroups = {"show"}
     * )
     */
    public function showAction(Item $item)
    {
        return $item;
    }
}

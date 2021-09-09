<?php

namespace App\Representation;

use App\Repository\ItemRepository;
use Hateoas\Representation\PaginatedRepresentation;
use Hateoas\Representation\CollectionRepresentation;

/**
 * Class ItemsListPaginator
 * @package App\Representation
 */
class ItemsListPaginator
{
    /**
     * @var ItemRepository
     */
    private ItemRepository $itemRepository;

    /**
     * ItemsListPaginator constructor.
     * @param ItemRepository $itemRepository
     */
    public function __construct(ItemRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    /**
     * Returns a paginated representation of an items list.
     * @param int $page
     * @param int $limit
     * @return PaginatedRepresentation
     */
    public function getPaginatedList(
        ?int $page = 1,
        ?int $limit = 10
    ): PaginatedRepresentation {

        $allItems = $this->itemRepository->findAll();

        $itemsToDisplay = $this->itemRepository->findBy(
            [],
            null,
            $limit,
            ($page - 1) * $limit
        );

        $nb_of_pages = (int) ceil(count($allItems) / $limit);
        $nb_of_items = (int) count($allItems);

        $collection = new CollectionRepresentation($itemsToDisplay);

        $paginatedList = new PaginatedRepresentation(
            $collection,
            'api_items_list',
            [],
            $page,
            $limit,
            $nb_of_pages,
            null,
            null,
            true,
            $nb_of_items
        );

        return $paginatedList;
    }
}

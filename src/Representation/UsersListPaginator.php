<?php

namespace App\Representation;

use App\Entity\Client;
use App\Repository\UserRepository;
use Hateoas\Representation\PaginatedRepresentation;
use Hateoas\Representation\CollectionRepresentation;

/**
 * Class UsersListPaginator
 * @package App\Representation
 */
class UsersListPaginator
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * UsersListPaginator constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Returns a paginated representation of a client's users list.
     * @param Client $client
     * @param int $page
     * @param int $limit
     * @return PaginatedRepresentation
     */
    public function getPaginatedList(
        Client $client,
        ?int $page = 1,
        ?int $limit = 5
    ): PaginatedRepresentation {

        $totalItems = $this->userRepository->findBy(
            [
                'client' => $client
            ]
        );

        $itemsToDisplay = $this->userRepository->findBy(
            [
                'client' => $client
            ],
            null,
            $limit,
            ($page - 1) * $limit
        );

        $nb_of_pages = (int) ceil(count($totalItems) / $limit);
        $nb_of_items = (int) count($totalItems);

        $collection = new CollectionRepresentation($itemsToDisplay);

        $paginatedList = new PaginatedRepresentation(
            $collection,
            'api_users_list',
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

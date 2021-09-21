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

        $allClientUsers = $this->userRepository->findBy(
            [
                'client' => $client
            ]
        );
        $nb_of_users = (int) count($allClientUsers);
        $nb_of_pages = (int) ceil(($nb_of_users) / $limit);

        $usersToDisplay = $this->userRepository->findBy(
            [
                'client' => $client
            ],
            null,
            $limit,
            ($page - 1) * $limit
        );


        $collection = new CollectionRepresentation($usersToDisplay);

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
            $nb_of_users
        );

        //To test the cache
        // sleep(2);

        return $paginatedList;
    }
}

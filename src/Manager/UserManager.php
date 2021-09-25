<?php

namespace App\Manager;

use App\Entity\User;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class UserManager
 * @package App\Manager
 */
class UserManager
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * UserManager contructor
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * To store a user in the database
     *
     * @param User $user
     * @param Client $client
     * @return void
     */
    public function recordUser(User $user, Client $client)
    {
        $this->em->persist(
            $user
                ->setClient($client)
                ->setCreatedAt(new \DateTimeImmutable())
        );
        $this->em->flush();
    }

    /**
     * To remove a user from the database
     *
     * @param User $user
     * @return void
     */
    public function deleteUser(User $user)
    {
        $this->em->remove($user);
        $this->em->flush();
    }
}

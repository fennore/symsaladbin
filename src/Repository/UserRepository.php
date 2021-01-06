<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param string $username Username to look up
     *
     * @return App\Entity\User
     */
    public function loadUserByUsername($username)
    {
        return $this->findOneBy(['username' => $username]);
    }

    public function createUser(UserInterface $user)
    {
        $this->persistUser($user);
    }

    public function updateUser(UserInterface $user)
    {
        $this->persistUser($user);
    }

    public function deleteUser(UserInterface $user)
    {
        $this->getEntityManager()->remove($user);
    }

    /**
     * Creates or updates the User Entity data in the database.
     */
    protected function persistUser(UserInterface $user)
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}

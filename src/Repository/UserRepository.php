<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;

class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(RegistryInterface $registry)
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
        /* $qb = $this
          ->createQueryBuilder('u');
          $expr = $qb
          ->expr()
          ->eq('u.username', ':username');
          $qb
          ->where($expr)
          ->setParameter(':username', $username);

          return $qb->getQuery()->getResult(); */
        return $this->findOneBy(['username' => $username]);
    }

    /**
     * Writes User Entity to database.
     *
     * @param UserInterface $user
     */
    public function createUser(UserInterface $user)
    {
        $this->persistUser($user);
    }

    /**
     * Updates User Entity in database.
     *
     * @param UserInterface $user
     */
    public function updateUser(UserInterface $user)
    {
        $this->persistUser($user);
    }

    /**
     * Removes User Entity from database.
     *
     * @param UserInterface $user
     */
    public function deleteUser(UserInterface $user)
    {
        $this->getEntityManager()->remove($user);
    }

    /**
     * Creates or updates the User Entity data in the database.
     *
     * @param UserInterface $user
     */
    protected function persistUser(UserInterface $user)
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}

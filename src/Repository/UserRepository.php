<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /*
      public function findBySomething($value)
      {
      return $this->createQueryBuilder('u')
      ->where('u.something = :value')->setParameter('value', $value)
      ->orderBy('u.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */
    
    /**
     * @param string $username Username to look up
     * @return App\Entity\User
     */
    public function loadUserByUsername($username)
    {
        $qb = $this
            ->createQueryBuilder('u');
        $expr = $qb
            ->expr()
            ->eq('u.username', ':username');
        $qb
            ->where($expr)
            ->setParameter(':username', $username);

        return $qb->getQuery()->getResult();
    }
    
    /**
     * Writes User Entity to database
     * @param User $user
     */
    public function createUser(UserInterface $user) {
        $this->persistUser($user);
    }

    /**
     * Updates User Entity in database
     * @param User $user
     */
    public function updateUser(UserInterface $user) {
        $this->persistUser($user);
    }
    
    /**
     * Removes User Entity from database
     * @param User $user
     */
    public function deleteUser(UserInterface $user) {
        $this->getEntityManager()
            ->remove($user);
    }
    
    /**
     * Creates or updates the User Entity data in the database.
     * @param User $user
     */
    protected function persistUser(UserInterface $user) {
        $this->getEntityManager()
            ->persist($user)
            ->flush();
    }
}

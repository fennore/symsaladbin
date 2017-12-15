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

}

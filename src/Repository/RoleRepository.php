<?php

namespace App\Repository;

use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMInvalidArgumentException;

class RoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    /*
      public function findBySomething($value)
      {
      return $this->createQueryBuilder('r')
      ->where('r.something = :value')->setParameter('value', $value)
      ->orderBy('r.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */

    /**
     * @param string $name Name for the role to search for. Each role has a unique name.
     */
    public function loadRoleByName(string $name): Role
    {
        return $this->findOneBy(['name' => $name]);
    }

    /**
     * Writes a new Role Entity to database.
     */
    public function createRole(Role $role)
    {
        if (!is_null($role->getId())) {
            throw ORMInvalidArgumentException::scheduleInsertForManagedEntity($role);
        }
        $this->persistRole($role);
    }

    /**
     * Updates Role Entity in database.
     */
    public function updateRole(Role $role)
    {
        if (is_null($role->getId())) {
            throw ORMInvalidArgumentException::entityHasNoIdentity($role, 'update');
        }
        $this->persistRole($role);
    }

    /**
     * Removes Role Entity from database.
     */
    public function deleteRole(Role $role)
    {
        $this->getEntityManager()->remove($role);
    }

    /**
     * Creates or updates the Role Entity data in the database.
     */
    protected function persistRole(Role $role)
    {
        $this->getEntityManager()->persist($role);
        $this->getEntityManager()->flush();
    }
}

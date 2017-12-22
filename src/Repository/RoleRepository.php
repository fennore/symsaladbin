<?php

namespace App\Repository;

use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\ORMInvalidArgumentException;

class RoleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
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
     * Writes a new Role Entity to database
     * @param Role $role
     */
    public function createRole(Role $role) {
        if(!is_null($role->getId())) {
            throw ORMInvalidArgumentException::scheduleInsertForManagedEntity($role);
        }
        $this->persistRole($role);
    }

    /**
     * Updates Role Entity in database
     * @param Role $role
     */
    public function updateRole(Role $role) {
        if(is_null($role->getId())) {
            throw ORMInvalidArgumentException::entityHasNoIdentity($role, 'update');
        }
        $this->persistRole($role);
    }
    
    /**
     * Removes Role Entity from database
     * @param Role $role
     */
    public function deleteRole(Role $role) {
        $this->getEntityManager()->remove($role);
    }
    
    /**
     * Creates or updates the Role Entity data in the database.
     * @param Role $role
     */
    protected function persistRole(Role $role) {
        $this->getEntityManager()->persist($role);
        $this->getEntityManager()->flush();
    }
}

<?php

namespace App\Repository\Traits;

trait RepositoryGeneralTrait
{
    /**
     * Alternative to the explicit truncate table sql command.
     * This works with delete instead which is more consistent across different database types.
     * Sqlite does not know the truncate command for example. For others truncate does not work consistently regarding transactions.
     */
    public function truncateTable()
    {
        // Flush execution and clear
        $this->getEntityManager()->flush();
        $this->clear();

        // Truncate
        $qb = $this->createQueryBuilder('rgt');

        $qb
            ->delete()
            ->getQuery()
            ->execute();

        // Reset auto increment => not useful for sqlite and even unknown there
        // @todo find a way to execute this for mysql (or non-sqlite)
        /*
        $this
            ->getEntityManager()
            ->getConnection()
            ->prepare('ALTER TABLE `'.$this->getClassMetadata()->getTableName().'` AUTO_INCREMENT = 1;')
            ->execute();**/
    }

    /**
     * Get the total number of records for a specific entity.
     *
     * @return int
     */
    public function getTotal(): int
    {
        return (int) $this
            ->createQueryBuilder('rgt')
            ->select('count(*)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}

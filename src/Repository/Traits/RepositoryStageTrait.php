<?php

namespace App\Repository\Traits;

/**
 * This is a Repository Trait and expects to be used within Repository classes,
 * that minimally inherit from Doctrine\Bundle\DoctrineBundle\Repository.
 */
trait RepositoryStageTrait
{
    /**
     * Remove all records related to a stage.
     *
     * @param int $stage
     */
    public function clearStage(int $stage)
    {
        // Flush execution and clear
        $this->getEntityManager()->flush();
        $this->clear();

        // Clear stage
        $qb = $this->createQueryBuilder('rst');

        $qb
            ->delete()
            ->where($qb->expr()->eq('rst.stage', ':stage'))
            ->setParameter(':stage', $stage)
            ->getQuery()
            ->execute();
    }
}

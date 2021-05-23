<?php declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\QueryBuilder;

trait StageRepositoryTrait
{
    /**
     * Remove all records related to a stage.
     */
    public function clearStage(int $stage): void
    {
        $qb = $this->createQueryBuilder('rst');

        $qb
            ->delete()
            ->where($qb->expr()->eq('rst.stage', ':stage'))
            ->setParameter(':stage', $stage)
            ->getQuery()
            ->execute();
    }

    abstract private function createQueryBuilder(): QueryBuilder;
}

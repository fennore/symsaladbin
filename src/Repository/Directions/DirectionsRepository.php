<?php declare(strict_types=1);

namespace App\Repository\Directions;

use Generator;
use App\Entity\Directions;
use App\Repository\StageRepositoryTrait;
use Doctrine\ORM\{EntityManagerInterface,EntityRepository,QueryBuilder};
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\ORM\Query\Expr\Join;

final class DirectionsRepository implements DirectionsRepositoryInterface
{
    use StageRepositoryTrait;
    
    private EntityRepository $innerRepository;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        $this->innerRepository = $entityManager->getRepository(Directions::class);
    }

    public function findByStage(int $stage): Generator
    {
        $qb = $this->createQueryBuilder('d');
        $qb
            ->join('d.origin', 'l', Join::ON, 'd.stage = :stage')
            ->setParameter(':stage', $stage)
            ->orderBy('l.weight', 'ASC');

        foreach ($qb->getQuery()->iterate() as $row) {
            yield $row[0];
        }
    }

    public function persist(Directions ...$directoins): void
    {
        $this->innerRepository->createQueryBuilder($alias)
    }

    public function remove(Directions ...$directoins): void
    {
        
    }

    private function createQueryBuilder($alias): QueryBuilder
    {
        
    }
}

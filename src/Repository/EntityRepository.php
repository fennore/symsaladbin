<?php declare(strict_types=1);

namespace App\Repository;

use Traversable;
use App\Entity\EntityInterface;
use Doctrine\ORM\{EntityManagerInterface, QueryBuilder};

class EntityRepository implements EntityRepositoryInterface
{
    private $innerRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->innerRepository = $entityManager->getRepository($className);
      
    }
    
    public function get(string $uniqueReference): EntityInterface
    {
        
    }

    public function getAll(): Traversable
    {
        
    }

    public function getRange(int $limit, int $offset = 0): Traversable
    {
        
    }
    
    public function create(EntityInterface $entity): void
    {
        
    }

    public function delete(EntityInterface $entity): void
    {
        
    }

    public function update($entity): void
    {
        
    }

    public function countAll(): int
    {
        return (int) $this
            ->createQueryBuilder('rgt')
            ->select('count(rgt.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countByCriteria(Criteria $criteria): int
    {
        $queryBuilder = $this
                ->createQueryBuilder('rgt')
                ->addCriteria($criteria)
        return (new Paginator(
            , false
            ))
                ->count();
    }

    private function entityHasState(object $entity, int $state): bool
    {
        $entityState = $this->getEntityManager()->getUnitOfWork()->getEntityState(
            $entity,
            UnitOfWork::STATE_DETACHED
        );

        return $state === $entityState;
    }
    /**
     * Alternative to the explicit truncate table sql command.
     * This works with delete instead which is more consistent across different database types.
     * Sqlite does not know the truncate command for example. For others truncate does not work consistently regarding transactions.
     */
    public function truncateTable(): void
    {
        $qb = $this->createQueryBuilder('rgt');

        $qb
            ->delete()
            ->getQuery()
            ->execute();

        // @see https://www.designcise.com/web/tutorial/how-to-reset-autoincrement-number-sequence-in-sqlite
        $this
            ->getEntityManager()
            ->getConnection()
            ->prepare("DELETE FROM `sqlite_sequence` WHERE `name` = '{$this->getClassMetadata()->getTableName()}';")
            ->execute();
    }

    public function createQueryBuilder()
    {
        
    }
}

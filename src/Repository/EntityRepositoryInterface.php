<?php declare(strict_types=1);

namespace App\Repository;

use Traversable;
use App\Entity\EntityInterface;
use Doctrine\Common\Collections\Criteria;

interface EntityRepositoryInterface
{
    public function get(string $uniqueReference): EntityInterface;
    public function getRange(int $limit, int $offset = 0): Traversable;
    public function getAll(): Traversable;
    public function countAll(): int;
    public function countByCriteria(Criteria $criteria): int;
    public function truncateTable(): void;
    public function transactional(): void;
    public function flush(): void;
    public function createQueryBuilder();
}

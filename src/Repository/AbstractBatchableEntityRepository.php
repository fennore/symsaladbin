<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Handler\DbBatchHandler;

abstract class AbstractBatchableEntityRepository extends ServiceEntityRepository
{
    use Traits\RepositoryGeneralTrait;
    /**
     * @var DbBatchHandler
     */
    protected $batchHandler;

    public function __construct(RegistryInterface $registry, DbBatchHandler $batchHandler, $entityClass)
    {
        $this->batchHandler = $batchHandler;

        parent::__construct($registry, $entityClass);
    }

    protected function startTransaction($useBatch)
    {
        $em = $this->getEntityManager();
        if ($useBatch) {
            $this->batchHandler->addToBatch($em, $this->getEntityName());
        } else {
            $em->flush();
        }
    }
}

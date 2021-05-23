<?php

namespace App\Repository\File;

use App\Entity\File;
use App\Handler\DbBatchHandler;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMInvalidArgumentException;
use Traversable;

final class FileRepository extends AbstractBatchableEntityRepository
{
    public function __construct(ManagerRegistry $registry, DbBatchHandler $batchHandler)
    {
        parent::__construct($registry, $batchHandler, File::class);
    }

    /**
     * Get all Files from database,
     * optionally filtered by parameter.
     * About IterableResult annoyance @see https://github.com/doctrine/doctrine2/issues/5287.
     */
    public function getFiles(array|string $mimeMatch = null, string $pathMatch = ''): Traversable
    {
        $qb = $this->createQueryBuilder('f');

        if (is_string($mimeMatch)) {
            $expr = $qb->expr()->like('f.mimeType', ':type');
        } elseif (is_array($mimeMatch)) {
            $expr = $qb->expr()->in('f.mimeType', ':type');
        }
        if (!empty($expr)) {
            $qb->setParameter(':type', $mimeMatch);
        }
        if (!empty($pathMatch)) {
            $expr = $qb->expr()->andX($expr, $qb->expr()->eq('f.path', ':path'));
            $qb->setParameter(':path', $pathMatch);
        }

        if (!empty($expr)) {
            $qb->where($expr);
        }

        foreach ($qb->getQuery()->iterate() as $row) {
            yield $row[0];
        }
    }

    /**
     * Writes a new File Entity to database.
     */
    public function createFile(File $file, $useBatch = true): void
    {
        if (!is_null($file->getId())) {
            throw ORMInvalidArgumentException::scheduleInsertForManagedEntity($file);
        }
        $this->persistFile($file, $useBatch);
    }

    /**
     * Updates File Entity in database.
     */
    public function updateFile(File $file, $useBatch = true): void
    {
        if (is_null($file->getId())) {
            throw ORMInvalidArgumentException::entityHasNoIdentity($file, 'update');
        }
        $this->persistFile($file, $useBatch);
    }

    /**
     * Removes File Entity from database.
     */
    public function deleteFile(File $file, $useBatch = true): void
    {
        $em = $this->getEntityManager();
        $em->remove($file);
        $this->startTransaction($useBatch);
    }

    /**
     * Creates or updates the File Entity data in the database.
     */
    protected function persistFile(File $file, $useBatch): void
    {
        $em = $this->getEntityManager();
        $em->persist($file);
        $this->startTransaction($useBatch);
    }
}

<?php

namespace App\Repository;

use App\Entity\File;
use App\Handler\DbBatchHandler;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMInvalidArgumentException;
use Traversable;

class FileRepository extends AbstractBatchableEntityRepository
{
    public function __construct(ManagerRegistry $registry, DbBatchHandler $batchHandler)
    {
        parent::__construct($registry, $batchHandler, File::class);
    }

    /**
     * Get all Files from database,
     * optionally filtered by parameter.
     * About IterableResult annoyance @see https://github.com/doctrine/doctrine2/issues/5287.
     *
     * @param string|array $mimeMatch
     */
    public function getFiles($mimeMatch = null, $pathMatch = ''): Traversable
    {
        $qb = $this->createQueryBuilder('f');
        // Build Expr
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
        // Set WHERE
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
    public function createFile(File $file, $useBatch = true)
    {
        if (!is_null($file->getId())) {
            throw ORMInvalidArgumentException::scheduleInsertForManagedEntity($file);
        }
        $this->persistFile($file, $useBatch);
    }

    /**
     * Updates File Entity in database.
     */
    public function updateFile(File $file, $useBatch = true)
    {
        if (is_null($file->getId())) {
            throw ORMInvalidArgumentException::entityHasNoIdentity($file, 'update');
        }
        $this->persistFile($file, $useBatch);
    }

    /**
     * Removes File Entity from database.
     */
    public function deleteFile(File $file, $useBatch = true)
    {
        $em = $this->getEntityManager();
        $em->remove($file);
        $this->startTransaction($useBatch);
    }

    /**
     * Creates or updates the File Entity data in the database.
     */
    protected function persistFile(File $file, $useBatch)
    {
        $em = $this->getEntityManager();
        $em->persist($file);
        $this->startTransaction($useBatch);
    }
}

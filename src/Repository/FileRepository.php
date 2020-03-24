<?php

namespace App\Repository;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Doctrine\ORM\ORMInvalidArgumentException;
use App\Handler\DbBatchHandler;
use App\Entity\File;

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
     *
     * @return IterableResult
     */
    public function getFiles($mimeMatch = null, $pathMatch = ''): IterableResult
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

        return $qb->getQuery()->iterate();
    }

    /**
     * Writes a new File Entity to database.
     *
     * @param File $file
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
     *
     * @param File $file
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
     *
     * @param File $file
     */
    public function deleteFile(File $file, $useBatch = true)
    {
        $em = $this->getEntityManager();
        $em->remove($file);
        $this->startTransaction($useBatch);
    }

    /**
     * Creates or updates the File Entity data in the database.
     *
     * @param File $file
     */
    protected function persistFile(File $file, $useBatch)
    {
        $em = $this->getEntityManager();
        $em->persist($file);
        $this->startTransaction($useBatch);
    }
}

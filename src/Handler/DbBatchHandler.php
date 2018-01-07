<?php

namespace App\Handler;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Handles database transaction execution in batches.
 */
class DbBatchHandler
{
    /**
     * @var array of ObjectManager
     */
    private $managers;

    /**
     * Array linking ObjectManager name with object class name.
     *
     * @var type
     */
    private $objectManagerMatch = [];

    /**
     * Size of each batch to process.
     *
     * @var int
     */
    private $batchProcessSize = 50;

    /**
     * Current count of batch items.
     *
     * @var array of integers
     */
    private $batchCount = [];

    public function __construct(RegistryInterface $registry)
    {
        $this->managers = $registry->getManagers();
        $this->batchCount = array_fill_keys(array_keys($this->managers), 0);
    }

    /**
     * Batch counter for given ObjectManager.
     * Automatically flushes the ObjectManager when enough operations are queued for transaction.
     *
     * @param ObjectManager $manager
     * @param string        $objectClassName ObjectManager related object
     */
    public function addToBatch(ObjectManager $manager, string $objectClassName)
    {
        // Get the ObjectManager name
        if (!isset($this->objectManagerMatch[$objectClassName])) {
            $managerName = array_search($manager, $this->managers);
            $this->objectManagerMatch[$objectClassName] = $managerName;
        } else {
            $managerName = $this->objectManagerMatch[$objectClassName];
        }
        // Batch size check
        if ($this->batchCount[$managerName] >= $this->batchProcessSize) {
            $this->processBatch($managerName);
        }
        ++$this->batchCount[$managerName];
    }

    /**
     * Process all batch leftovers.
     */
    public function cleanUpBatch()
    {
        array_walk($this->batchCount, function ($count, $managerName) {
            if ($count > 0) {
                $this->processBatch($managerName);
            }
        });
    }

    /**
     * Set the size of a batch.
     *
     * @param int $size
     */
    public function setBatchProcessSize(int $size)
    {
        $this->batchProcessSize = $size;
    }

    /**
     * @param string $objectClassName Fully qualified classname for the managed object
     *
     * @return int
     */
    public function getBatchCount(string $objectClassName)
    {
        $managerName = $this->objectManagerMatch[$objectClassName];

        return $this->batchCount[$managerName];
    }

    /**
     * Performs transaction on the object manager.
     *
     * @param string $managerName Name of the object manager
     */
    private function processBatch(string $managerName)
    {
        $this->managers[$managerName]->flush(); // Executes all deletions.
        $this->managers[$managerName]->clear(); // Detaches all objects from the manager.
        $this->batchCount[$managerName] = 0; // Set batch count back to 0
    }
}

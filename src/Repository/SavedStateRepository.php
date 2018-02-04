<?php

namespace App\Repository;

use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Handler\DbBatchHandler;
use App\States\StateInterface;
use App\Entity\SavedState;

class SavedStateRepository extends AbstractBatchableEntityRepository
{

    public function __construct(RegistryInterface $registry, DbBatchHandler $batchHandler)
    {
        parent::__construct($registry, $batchHandler, Location::class);
    }

    /**
     * Checks if a given State is already persistent as SavedState.
     * Returns a new or the already existing SavedState.
     * If the State exists the given State will change its reference to the existing state in SavedState.
     *
     * @param StateInterface $state
     *
     * @return SavedState
     */
    public function checkState(StateInterface &$state)
    {
        $savedState = $this->find($state->getKey()) ?? new SavedState($state);
        $state = $savedState->getState();

        return $this->mergeSavedState($savedState);
    }

    public function detachSavedState(SavedState $savedState)
    {
        $this->getEntityManager()->detach($savedState);
    }

    /**
     * Returns a merged copy of the SavedState.
     *
     * @param SavedState $savedState
     *
     * @return SavedState
     */
    public function mergeSavedState(SavedState $savedState): SavedState
    {
        return $this->getEntityManager()->merge($savedState);
    }

    /**
     * Writes a new SavedState Entity to database.
     *
     * @param SavedState $savedState
     */
    public function createSavedState(SavedState $savedState, $useBatch = true)
    {
        $this->persistSavedState($savedState, $useBatch);
    }

    /**
     * Updates SavedState Entity in database.
     *
     * @param SavedState $savedState
     */
    public function updateSavedState(SavedState $savedState, $useBatch = true)
    {
        $this->persistSavedState($savedState, $useBatch);
    }

    /**
     * Removes SavedState Entity from database.
     *
     * @param SavedState $savedState
     */
    public function deleteSavedState(SavedState $savedState, $useBatch = true)
    {
        $em = $this->getEntityManager();
        $em->remove($savedState);
        $this->startTransaction($useBatch);
    }

    /**
     * Creates or updates the SavedState Entity data in the database.
     *
     * @param SavedState $savedState
     */
    protected function persistSavedState(SavedState $savedState, $useBatch)
    {
        $em = $this->getEntityManager();
        $em->persist($savedState);
        $this->startTransaction($useBatch);
    }
}

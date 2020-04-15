<?php

namespace App\Repository;

use App\Entity\SavedState;
use App\Handler\DbBatchHandler;
use App\States\StateInterface;
use Doctrine\Common\Persistence\ManagerRegistry;

class SavedStateRepository extends AbstractBatchableEntityRepository
{
    public function __construct(ManagerRegistry $registry, DbBatchHandler $batchHandler)
    {
        parent::__construct($registry, $batchHandler, SavedState::class);
    }

    /**
     * Checks if a given State is already persistent as SavedState.
     * Returns a new or the already existing SavedState.
     * If the State exists the given State will change its reference to the existing state in SavedState.
     *
     * @return SavedState
     */
    public function checkState(StateInterface &$state)
    {
        $savedState = $this->mergeSavedState($this->find($state->getKey()) ?? new SavedState($state));
        $state = $savedState->getState();

        return $savedState;
    }

    public function detachSavedState(SavedState $savedState)
    {
        $this->getEntityManager()->detach($savedState);
    }

    /**
     * Returns a merged copy of the SavedState.
     */
    public function mergeSavedState(SavedState $savedState): SavedState
    {
        return $this->getEntityManager()->merge($savedState);
    }

    /**
     * Writes a new SavedState Entity to database.
     */
    public function createSavedState(SavedState $savedState, $useBatch = true)
    {
        $this->persistSavedState($savedState, $useBatch);
    }

    /**
     * Updates SavedState Entity in database.
     * Note: will update regardless if anything changed or not.
     */
    public function updateSavedState(SavedState $savedState, $useBatch = true)
    {
        // Force Doctrine to update state, because it does not notice any changes
        if (!empty($this->getEntityManager()->getUnitOfWork()->getOriginalEntityData($savedState))) {
            $this->getEntityManager()->getUnitOfWork()->setOriginalEntityProperty(spl_object_hash($savedState), 'state', null);
        }

        $this->persistSavedState($savedState, $useBatch);
    }

    /**
     * Removes SavedState Entity from database.
     */
    public function deleteSavedState(SavedState $savedState, $useBatch = true)
    {
        $em = $this->getEntityManager();
        $em->remove($savedState);
        $this->startTransaction($useBatch);
    }

    /**
     * Creates or updates the SavedState Entity data in the database.
     */
    protected function persistSavedState(SavedState $savedState, $useBatch)
    {
        $em = $this->getEntityManager();
        $em->persist($savedState);
        $this->startTransaction($useBatch);
    }
}

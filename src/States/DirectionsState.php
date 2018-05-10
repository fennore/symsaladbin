<?php

namespace App\States;

/**
 * Keeps track of the Directions application state.
 */
class DirectionsState implements StateInterface
{
    const KEY = 589;

    /**
     * The current stage to calculate directions for.
     *
     * @var int
     */
    private $stage;

    /**
     * The last weight within the stage that has been used for calculations.
     * This is also the weight to start from for the next calculation.
     *
     * @var int
     */
    private $weight;

    /**
     * When an update is in progress these will hold the values for the update status.
     */
    private $updateStage;
    private $updateWeight;
    private $updateRoute;

    /**
     * List of stage numbers that wait to be updated.
     */
    private $pendingUpdates = [];

    public function getKey(): int
    {
        return self::KEY;
    }

    /**
     * Set the stage number.
     * This will be the stage used to calculate directions for.
     */
    public function setStage(int $stage)
    {
        $this->stage = $stage;

        return $this;
    }

    /**
     * Set the weight within the stage.
     * This will be the start location for the next directions calculation.
     */
    public function setWeight(int $weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Add given stage number as pending for update.
     */
    public function addPendingUpdate($stage)
    {
        if (!in_array($stage, $this->pendingUpdates)) {
            array_push($this->pendingUpdates, $stage);
        }

        return $this;
    }

    /**
     * Get the current stage being updated with directions.
     *
     * @return int|null
     */
    public function getCurrentUpdate()
    {
        $this->setNextUpdate();

        return $this->updateStage;
    }

    public function addUpdatePolylines(array $polylines)
    {
        if (!is_null($this->getCurrentUpdate())) {
            array_merge($this->updateRoute, $polylines);
        }
    }

    public function getUpdatedRoute()
    {
        return $this->updateRoute;
    }

    public function setNextStage()
    {
        if (is_null($this->getCurrentUpdate())) {
            $this
                ->setWeight(0)
                ->setStage(++$this->stage);
        } else {
            $this->finishUpdate();
            $this->setNextUpdate();
        }
    }

    public function saveStageForContinuation(int $weight)
    {
        if (is_null($this->getCurrentUpdate())) {
            $this->setWeight($weight);
        } else {
            $this->updateWeight = $weight;
        }
    }

    /**
     * Set data for the next stage to update.
     * Does nothing when no stage requires updating, or there is still an ongoing update.
     */
    public function setNextUpdate()
    {
        if (!is_null($this->updateStage)) {
            return $this;
        }
        if (empty($this->pendingUpdates)) {
            return $this;
        }

        $this->updateStage = array_shift($this->pendingUpdates);
        $this->updateWeight = 0;
        $this->updateRoute = [];

        return $this;
    }

    public function finishUpdate()
    {
        $this->updateStage = null;
        $this->updateWeight = null;
        $this->updateRoute = null;

        return $this;
    }

    /**
     * @return int
     */
    public function getStage(): int
    {
        if (is_null($this->getCurrentUpdate())) {
            return $this->stage ?? 1;
        } else {
            return $this->updateStage;
        }
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        if (is_null($this->getCurrentUpdate())) {
            return $this->weight ?? 0;
        } else {
            return $this->updateWeight;
        }
    }
}

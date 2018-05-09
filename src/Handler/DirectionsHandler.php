<?php

namespace App\Handler;

use App\Repository\LocationRepository;
use App\Repository\DirectionsRepository;
use App\Repository\SavedStateRepository;
use App\Driver\DirectionsDriverInterface;
use App\States\EncodedRoute;
use App\States\DirectionsState;

/**
 * Use a DirectionsDriver to calculate a route from Location entities.
 */
class DirectionsHandler
{
    /**
     * Because it's a lucky number? Nothing can ever go wrong!
     */
    const MAXREQUESTS = 13;

    /**
     * @var DirectionsDriverInterface
     */
    private $driver;

    /**
     * @var LocationRepository
     */
    private $locationRepository;

    /**
     * @var DirectionsRepository
     */
    private $directionsRepository;

    /**
     * @var SavedStateRepository
     */
    private $savedStateRepository;

    /**
     * @param DirectionsDriverInterface $driver         Available Directions Driver
     * @param DirectionsRepository      $directionsRepo
     * @param SavedStateRepository      $savedStateRepo
     */
    public function __construct(DirectionsDriverInterface $driver, LocationRepository $locationRepository, DirectionsRepository $directionsRepository, SavedStateRepository $savedStateRepository)
    {
        $this->driver = $driver;
        $this->locationRepository = $locationRepository;
        $this->directionsRepository = $directionsRepository;
        $this->savedStateRepository = $savedStateRepository;
    }

    /**
     * The maximum amount of Location entities that will be used for direction requests.
     *
     * @return int
     */
    private function getLocationLimit()
    {
        return $this->driver->getRequestSize() * self::MAXREQUESTS;
    }

    /**
     * Get the encoded route.
     *
     * @return array
     */
    public function getEncodedRoute(): array
    {
        $state = new EncodedRoute();
        $this->savedStateRepository->checkState($state);

        return $state->getRoute();
    }

    /**
     * Resets encoded route to certain point,
     * so that it can be rebuild in chunks.
     *
     * @todo See if we can use some direction/location diff to perform a more accurate reset.
     *
     * @param int $stage
     */
    public function resetEncodedRoute(int $stage)
    {
        // 1. Reset Direction SavedState to weight 0 and stage $stage
        // 2. Update Route SavedState removing all encoded routes of stage $stage only
    }

    public function rebuildEncodedRoute(int $stage)
    {
        $encodedRoute = [];
        // 1. State
        $state = new EncodedRoute();
        $savedState = $this->savedStateRepository->checkState($state);
        // 2. Directions
        $directions = $this->directionsRepository->getDirections($stage);
        // 3. Encoded route
        // foreach iteration instead $encodedRoute = array_map(array($this->driver, 'getPolyline'), $directionsList);
        foreach ($directions as $row) {
            $encodedRoute[] = $this->driver->getPolyline($row[0]);
        }
        $state->setStage($stage, $encodedRoute);
        $this->savedStateRepository->updateSavedState($savedState);

        return $this;
    }

    /**
     * Build an encoded route in chunks from all Location entities.
     *
     * @return DirectionsState
     */
    public function buildEncodedRoute()
    {
        // 1. Get states
        $directionsState = new DirectionsState();
        $savedDirectionsState = $this->savedStateRepository->checkState($directionsState);
        $route = new EncodedRoute();
        $savedRoute = $this->savedStateRepository->checkState($route);
        $stage = $directionsState->getStage() ?? 1;
        $weight = $directionsState->getWeight() ?? 0;
        // 2. Get Locations
        $locationList = $this->locationRepository->getStageLocations($stage, $weight, $this->getLocationLimit());
        // - Stop processing when no locations are found and we ran out of stages to process
        if (!$locationList->valid() && $stage > $this->locationRepository->getLastStage()) {
            return;
        }
        // - Stop processing when weight is 0 but there are already encoded route parts
        if (0 === $weight && !empty($route->getStage($stage))) {
            // Also set Direction SavedState to next stage
            $directionsState->setStage(++$stage);
            $this->savedStateRepository->updateSavedState($savedDirectionsState);

            return;
        }
        // 3. Directions
        $directionsList = $this->driver->getDirections($locationList, self::MAXREQUESTS);
        // 4. Encoded route
        $encodedRoute = array_map(array($this->driver, 'getPolyline'), $directionsList);
        // 5. Set data for Db and Flush it
        //array_map(array($this->directionsRepository, 'createDirections'), $directionsList);
        $route->setStage($stage, $encodedRoute);

        $lastDirection = array_pop($directionsList);
        // Old check : match last location with last direction location + count check $locationCheck = $lastDirectionLocation == $lastLocation && $countCheck;
        if (!$locationList->valid()) {
            $directionsState
                ->setWeight(0)
                ->setStage(++$stage);
        } else {
            $directionsState
                ->setWeight($lastDirection->getDestination()->getWeight())
                ->setStage($stage);
        }

        // Note: merges are required because the objects might have been detached during writing the directions to database
        $this->savedStateRepository->updateSavedState($this->savedStateRepository->mergeSavedState($savedDirectionsState));
        $this->savedStateRepository->updateSavedState($this->savedStateRepository->mergeSavedState($savedRoute));

        return $directionsState;
    }
}

<?php

namespace App\Driver;

use App\Entity\{Directions,Location};
use Doctrine\ORM\Internal\Hydration\IterableResult;

/**
 * Interface for directions drivers used by the directions handler.
 */
interface DirectionsDriverInterface
{

    /**
     * Get the amount of locations that will be used in 1 Direction request.
     */
    public function getRequestSize(): int;

    /**
     * Get Route Directions.
     * @param IterableResult $locationList List of Location Entities.
     * @param int $maxRequests Maximum Direction requests to send
     * @return array
     */
    public function getDirections(IterableResult $locationList, $maxRequests = 0): array;
    
    /**
     * Get the last Location used for Direction requests
     * @return Location|null
     */
    public function getLastLocation(): ?Location;

    /**
     * Get encoded polyline.
     */
    public function getPolyline(Directions $directions): string;
}
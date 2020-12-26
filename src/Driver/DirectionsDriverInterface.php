<?php

namespace App\Driver;

use App\Entity\Directions;
use App\Entity\Location;
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
     *
     * @param IterableResult $locationList list of Location Entities
     * @param int            $maxRequests  Maximum Direction requests to send
     */
    public function getDirections(IterableResult $locationList, $maxRequests = 0): array;

    /**
     * If the directions calculation hit the request limit there will be locations left to calculate directions for.
     *
     * @return bool|null Returns null when no directions calculation has been done yet (= unknown)
     */
    public function hasUncalculatedDirectionsLeft(): ?bool;

    /**
     * Get the last Location used for Direction requests.
     */
    public function getLastLocation(): ?Location;

    /**
     * Get encoded polyline.
     */
    public function getPolyline(Directions $directions): string;
}

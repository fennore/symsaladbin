<?php

namespace App\Driver;

use App\Entity\Directions;
use App\Entity\Location;
use Doctrine\ORM\Internal\Hydration\IterableResult;

/**
 * Directions Driver using Google API.
 */
class GoogleApiDirectionsDriver implements DirectionsDriverInterface
{
    /**
     * Maximum amount of locations sent per 1 direction request.
     *
     * @see https://developers.google.com/maps/documentation/javascript/directions#UsageLimits
     */
    const REQUESTSIZE = 25;

    private $apiKey;

    /**
     * Keep track of the last Location Entity used in Directions Request.
     *
     * @var Location
     */
    private $lastLocation;

    /**
     * Array of leftover locations when directions calculation hit request limit.
     */
    private $leftovers;

    public function __construct()
    {
        $this->apiKey = $_ENV['APP_GOOGLEDIRECTIONS_APIKEY'];
    }

    public function getRequestSize(): int
    {
        return self::REQUESTSIZE;
    }

    /**
     * {@inheritdoc}
     */
    public function getDirections(IterableResult $locationList, $maxRequests = 0): array
    {
        $directionsList = $list = [];
        $setBack = $requestCount = 0;
        // Set first origin
        $origin = current($locationList->next());
        $destination = current($locationList->next());

        while (($locationList->valid() || !empty($list)) && (0 === $maxRequests || $requestCount < $maxRequests)) {
            $modes = Helper::DIRECTIONMODES;

            do {
                $locationList->current() ? array_push($list, current($locationList->current())) : null; // remember row[0] :(
            } while ($locationList->valid() && count($list) < self::REQUESTSIZE && $locationList->next());

            // Note: the origin must always be the same for every travel mode
            do {
                $modeSet = array_shift($modes);
                $mode = key($modeSet);
                $size = (current($modeSet) ?? self::REQUESTSIZE) - $setBack;
                $listChunk = array_slice($list, 0, $size);
                $destination = array_pop($listChunk);
                $directionsRequest = new DirectionsRequest($this->apiKey, $origin, $destination, $mode, 'ferries|tolls|highways');
                array_map([$directionsRequest, 'addWaypoint'], $listChunk);
                $response = $directionsRequest->getDirections();
                ++$requestCount;
            } while (empty($response->routes) && !empty($modes));

            array_splice($list, 0, $size);
            // Add direction to list
            if (!empty($response->routes)) {
                array_push($directionsList, new Directions($origin, $destination, $response));
            }
            // Set next origin as last destination
            $origin = $destination;
            $this->lastLocation = $destination;
            // From now on we reuse one location so we need one less from locationList
            $setBack = 1;
        }

        $this->leftovers = !empty($list) || $locationList->valid();

        return $directionsList;
    }

    /**
     * {@inheritdoc}
     */
    public function hasUncalculatedDirectionsLeft(): ?bool
    {
        return $this->leftovers;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastLocation(): ?Location
    {
        return $this->lastLocation;
    }

    /**
     * Get encoded polyline.
     * Currently returning the less accurate overview_polyline.
     *
     * @todo https://stackoverflow.com/questions/16180104/get-a-polyline-from-google-maps-directions-v3
     */
    public function getPolyline(Directions $directions): string
    {
        return (string) $directions->getData()->routes[0]->overview_polyline->points;
    }
}
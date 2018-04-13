<?php

namespace App\Driver\Gapi;

use Doctrine\ORM\Internal\Hydration\IterableResult;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Driver\DirectionsDriverInterface;
use App\Entity\{Directions,Location};

/**
 * Directions Driver using Google API
 */
class GapiDirectionsDriver implements DirectionsDriverInterface
{
    /**
     * Maximum amount of locations sent per 1 direction request
     * @see https://developers.google.com/maps/documentation/javascript/directions#UsageLimits
     */
    const REQUESTSIZE = 25;

    private $apiKey;

    /**
     * Keep track of the last Location Entity used in Directions Request.
     * @var Location 
     */
    private $lastLocation;

    public function __construct(ContainerInterface $container)
    {
        $this->apiKey = $container->getParameter('app.googledirections.apikey');
    }

    public function getRequestSize(): int
    {
        return self::REQUESTSIZE;
    }

    /**
     * {@inheritDoc}
     */
    public function getDirections(IterableResult $locationList, $maxRequests = 0): array
    {
        $directionsList = $list = [];
        $setBack = $requestCount = 0;
        // Set first origin
        $origin = current($locationList->next());
        $destination = current($locationList->next());

        while ($locationList->valid() && ($maxRequests === 0 || $requestCount < $maxRequests)) {

            $modes = GapiHelper::DIRECTIONMODES;
            do {
                array_push($list, current($locationList->current())); // remember row[0] :(
            } while (count($list) < self::REQUESTSIZE && $locationList->next());
            // Note: the origin must always be the same for every travel mode
            do {
                $modeSet = array_shift($modes);
                $mode = key($modeSet);
                $size = (current($modeSet) ?? self::REQUESTSIZE) - $setBack;
                $listChunk = array_slice($list, 0, $size);
                $destination = array_pop($listChunk);
                $directionsRequest = new GapiDirectionsRequest($this->apiKey, $origin, $destination, $mode, 'ferries|tolls|highways');
                array_map(array($directionsRequest, 'addWaypoint'), $listChunk);
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
        return $directionsList;
    }

    /**
     * {@inheritDoc}
     */
    public function getLastLocation(): ?Location
    {
        return $this->lastLocation;
    }

    /**
     * Get encoded polyline.
     * Currently returning the less accurate overview_polyline.
     * @todo https://stackoverflow.com/questions/16180104/get-a-polyline-from-google-maps-directions-v3
     */
    public function getPolyline(Directions $directions): string
    {
        return (string) $directions->getData()->routes[0]->overview_polyline->points;
    }
}

<?php

namespace App\Driver\GoogleApi\Directions;

use App\Entity\Location;
use ErrorException;

/**
 * Represents a google API directions request using waypoints.
 *
 * @see https://developers.google.com/maps/documentation/directions/intro#Waypoints
 */
class Request
{
    /**
     * @var string Google API authentication key
     */
    private $apiKey;

    /**
     * Directions mode (fe BICYCLE | WALKING).
     *
     * @var string
     */
    private $mode;

    /**
     * @var Location
     */
    private $origin;

    /**
     * @var Location
     */
    private $destination;

    /**
     * Array of Location entities.
     *
     * @var Location[]
     */
    private $waypoints = [];

    /**
     * @var string
     */
    private $avoid;

    /**
     * @param string $mode  Optional, defaults to GAPI default (driving)
     * @param string $avoid Optional, defaults to GAPI default (none)
     *
     * @throws ErrorException when invalid directions mode is used
     */
    public function __construct(string $apiKey, Location $origin, Location $destination, string $mode = null, string $avoid = null)
    {
        if (!in_array($mode, Helper::VALIDDIRECTIONMODES)) {
            throw new ErrorException(sprintf('Trying to set invalid mode % for GAPI Direction Request.', $mode));
        }
        $this->apiKey = $apiKey;
        $this->origin = $origin;
        $this->destination = $destination;
        $this->mode = $mode;
        $this->avoid = $avoid;
    }

    public function addWaypoint(Location $waypoint)
    {
        array_push($this->waypoints, $waypoint);
    }

    public function getDirections()
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => Helper::DIRECTIONSREQUESTURL.'?'.$this,
            CURLOPT_SSL_VERIFYPEER => false, // ssl verification seems to fail
        ]);

        $response = json_decode(curl_exec($curl));

        curl_close($curl);

        usleep(100);

        return $response;
    }

    public function __toString()
    {
        return http_build_query([
            'mode' => $this->mode,
            'origin' => (string) $this->origin,
            'destination' => (string) $this->destination,
            'waypoints' => 'via:'.(implode('|via:', $this->waypoints)),
            'avoid' => $this->avoid,
            'key' => $this->apiKey,
        ]);
    }
}
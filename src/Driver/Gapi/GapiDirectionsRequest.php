<?php

namespace App\Driver\Gapi;

use ErrorException;
use App\Entity\Location;

/**
 * Represents a google API directions request using waypoints.
 * @see https://developers.google.com/maps/documentation/directions/intro#Waypoints
 */
class GapiDirectionsRequest
{
    /**
     * @var string Google API authentication key
     */
    private $apiKey;
    
    /**
     * Directions mode (fe BICYCLE | WALKING).
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
     * Array of Location entities
     * @var array 
     */
    private $waypoints = [];

    /**
     * @var string 
     */
    private $avoid;

    /**
     * @param string $apiKey
     * @param Location $origin
     * @param Location $destination
     * @param string $mode Optional, defaults to GAPI default (driving)
     * @param string $avoid Optional, defaults to GAPI default (none)
     * @throws ErrorException When invalid directions mode is used.
     */
    public function __construct(string $apiKey, Location $origin, Location $destination, string $mode = null, string $avoid = null)
    {
        if (!in_array($mode, GapiHelper::VALIDDIRECTIONMODES)) {
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

        $url = GapiHelper::DIRECTIONSREQUESTURL.'?'.$this;
        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => false, // ssl verification seems to fail
        ));
        // Send the request & save response to $resp
        $response = json_decode(curl_exec($curl));
        // Close request to clear up some resources
        curl_close($curl);
        // Take a break;
        usleep(100);
        return $response;
    }

    public function __toString()
    {
        return http_build_query(array(
            'mode' => $this->mode,
            'origin' => (string) $this->origin,
            'destination' => (string) $this->destination,
            'waypoints' => 'via:'.(implode('|via:', $this->waypoints)),
            'avoid' => $this->avoid,
            'key' => $this->apiKey,
        ));
    }
}

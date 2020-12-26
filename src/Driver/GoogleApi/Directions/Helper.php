<?php

namespace App\Driver\Gapi;

class GapiHelper
{
    const VALIDDIRECTIONMODES = [
        'bicycling',
        'walking',
        'driving',
    ];

    /**
     * Direction modes to query with fallbacks and sizes.
     * If no size is given default will be used.
     */
    const DIRECTIONMODES = [
        ['bicycling' => null],
        ['bicycling' => 5],
        ['walking' => 5],
        ['driving' => 5],
    ];

    /**
     * Google Directions API request url with json response.
     */
    const DIRECTIONSREQUESTURL = 'https://maps.googleapis.com/maps/api/directions/json';
}

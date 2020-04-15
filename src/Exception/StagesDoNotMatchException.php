<?php

namespace App\Exception;

use App\Entity\Location;
use RuntimeException;

class StagesDoNotMatchException extends RuntimeException
{
    public function __construct(Location $originLocation, Location $compareLocation)
    {
        $message = 'Stage of location named "'.$originLocation->getName().'" being '.$originLocation->getStage().' does not match with the '.
            'stage of location named "'.$compareLocation->getName().'" being '.$compareLocation->getStage().'.';
        parent::__construct($message);
    }
}

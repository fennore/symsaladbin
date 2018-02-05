<?php

namespace App\States;

class EncodedRoute implements StateInterface
{
    const KEY = 874;
    
    /**
     * @var array 
     */
    private $route = [];
    
    public function getKey(): int
    {
        return self::KEY;
    }
    
    public function getRoute(): array
    {
        return $this->route;
    }
    
    public function getStage(int $stage): array
    {
        return $this->route['stage'.$stage] ?? [];
    }
    
    public function setRoute(array $encodedRoute)
    {
        $this->route = $encodedRoute;
    }
    
    public function setStage(int $stage, array $encodedRoute)
    {
        $this->route['stage'.$stage] = $encodedRoute;
        return $this;
    }
    
    public function addPolyline(int $stage, string $polyline)
    {
        array_push($this->route['stage'.$stage], $polyline);
        return $this;
    }
}

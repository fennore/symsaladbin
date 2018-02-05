<?php

namespace App\States;

class DirectionsState implements StateInterface
{
    const KEY = 589;
    
    private $stage;
    
    private $weight;
    
    public function getKey(): int
    {
        return self::KEY;
    }
    
    public function setStage(int $stage)
    {
        $this->stage = $stage;
        return $this;
    }
    
    public function setWeight(int $weight)
    {
        $this->weight = $weight;
        return $this;
    }
    
    /**
     * @return int|null
     */
    public function getStage(): ?int
    {
        return $this->stage;
    }
    
    /**
     * @return int|null
     */
    public function getWeight(): ?int
    {
        return $this->weight;
    }
}

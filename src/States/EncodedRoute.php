<?php

namespace App\States;

class EncodedRoute implements StateInterface
{
    const KEY = 874;
    
    public function getKey(): int
    {
        return self::KEY;
    }
    
    public function getRoute(): array
    {
        return [];
    }
}

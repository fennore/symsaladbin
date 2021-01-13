<?php

namespace App\Entity;

use Stringable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Coordinate implements Stringable
{

    #[ORM\Column(type:'float')]
    private float $lat;

    #[ORM\Column(type:'float')]
    private float $lng;

    public function __construct(float $lat, float $lng)
    {
        $this->lat = $lat;
        $this->lng = $lng;
    }

    public function setLat(float $lat): static
    {
        $this->lat = $lat;

        return $this;
    }

    public function setLng(float $lng): static
    {
        $this->lng = $lng;

        return $this;
    }

    public function getLat(): float
    {
        return $this->lat;
    }

    public function getLng(): float
    {
        return $this->lng;
    }

    public function __toString()
    {
        return "{$this->getLat()},{$this->getLng()}";
    }
}

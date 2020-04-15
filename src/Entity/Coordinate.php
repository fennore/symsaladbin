<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Coordinate
{
    /**
     * @ORM\Column(type="float")
     */
    private $lat;

    /**
     * @ORM\Column(type="float")
     */
    private $lng;

    public function __construct(float $lat, float $lng)
    {
        $this->lat = $lat;
        $this->lng = $lng;
    }

    /**
     * @param float $lat Latitude
     *
     * @return $this
     */
    public function setLat(float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * @param float $lng Longitude
     *
     * @return $this
     */
    public function setLng(float $lng): self
    {
        $this->lng = $lng;

        return $this;
    }

    public function getLat()
    {
        return (float) $this->lat;
    }

    public function getLng()
    {
        return (float) $this->lng;
    }

    public function __toString()
    {
        return $this->getLat().','.$this->getLng();
    }
}

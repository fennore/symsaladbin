<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Coordinate;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocationRepository")
 */
class Location
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Embedded(class="App\Entity\Coordinate", columnPrefix=false)
     */
    private $coordinate;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $weight;

    /**
     * @ORM\Column(type="smallint", options={"unsigned":true})
     */
    private $status;

    /**
     * @ORM\Column(type="smallint", options={"unsigned":true})
     */
    private $stage;

    /**
     * @param Coordinate $coordinate
     * @param string $name
     * @param int $stage
     * @param int $weight
     * @param int $status
     */
    public function __construct(Coordinate $coordinate, string $name, int $stage, int $weight = 0, int $status = 1)
    {
        $this->coordinate = $coordinate;
        $this->name = $name;
        $this->weight = $weight;
        $this->status = $status;
        $this->stage = $stage;
    }

    public function getCoordinate(): Coordinate
    {
        return $this->coordinate;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStage(): int
    {
        return (int) $this->stage;
    }

    public function getWeight(): int
    {
        return (int) $this->weight;
    }

    public function getStatus(): int
    {
        return (int) $this->status;
    }

    public function __toString(): string
    {
        return (string) $this->coordinate;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

}

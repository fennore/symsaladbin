<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Stringable;

#[ORM\Entity(repositoryClass:'App\Repository\LocationRepository')]
#[ORM\Table(name:'location', indexes:[#[ORM\Index(name:'location_list_select', columns:['stage', 'weight'])], #[ORM\Index(name:'location_filter', columns:['status'])]])]
final class Location implements Stringable
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:'integer')]
    private int $id;

    #[ORM\Embedded(class:'App\Entity\Coordinate', columnPrefix:false)]
    private Coordinate $coordinate;

    #[ORM\Column(type:'string')]
    private string $name;

    #[ORM\Column(type:'integer')]
    private int $weight;

    #[ORM\Column(type:'smallint', options:["unsigned" => true])]
    private int $status;

    #[ORM\Column(type:'smallint', options:["unsigned" => true])]
    private int $stage;

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
        return $this->stage;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function __toString(): string
    {
        return $this->coordinate;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}

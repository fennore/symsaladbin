<?php

namespace App\Entity;

use App\Exception\StagesDoNotMatchException;
use Doctrine\ORM\Mapping as ORM;
use stdClass;

#[ORM\Table(name:'directions')]
#[ORM\Entity(repositoryClass:'App\Repository\DirectionsRepository')]
#[ORM\HasLifecycleCallbacks]
class Directions implements EntityInterface
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:'integer')]
    private int $id;

    #[ORM\OneToOne(targetEntity:'App\Entity\Location')]
    private Location $origin;

    #[ORM\OneToOne(targetEntity:'App\Entity\Location')]
    private Location $destination;

    #[ORM\Column(type:'smallint', options:['unsigned' => true])]
    private int $stage;

    #[ORM\Column(type:'json')]
    private stdClass $data;

    /**
     * Called on postLoad Entity life cycle.
     * Because doctrine converts json objects to associative arrays instead of objects and we want objects, which is default php behaviour!
     *
     * @see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#lifecycle-callbacks
     */
    #[ORM\PostLoad]
    public function postLoad()
    {
        $this->data = json_decode(json_encode($this->data));
    }

    /** @param stdClass $data Json directions information */
    public function __construct(Location $origin, Location $destination, stdClass $data)
    {
        $this->origin = $origin;
        $this->destination = $destination;
        if ($origin->getStage() !== $destination->getStage()) {
            throw new StagesDoNotMatchException($origin, $destination);
        }
        $this->stage = $origin->getStage();
        $this->data = $data;
    }

    public function getOrigin(): Location
    {
        return $this->origin;
    }

    public function getDestination(): Location
    {
        return $this->destination;
    }

    public function getData(): stdClass
    {
        return $this->data;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}

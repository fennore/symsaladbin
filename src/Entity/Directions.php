<?php

namespace App\Entity;

use stdClass;
use Doctrine\ORM\Mapping as ORM;
use App\Exception\StagesDoNotMatchException;

/**
 * @ORM\Table(name="directions")
 * @ORM\Entity(repositoryClass="App\Repository\DirectionsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Directions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Location")
     * @var Location 
     */
    private $origin;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Location")
     * @var Location 
     */
    private $destination;

    /**
     * @ORM\Column(type="smallint", options={"unsigned":true})
     * @var int 
     */
    private $stage;

    /**
     * @ORM\Column(type="json")
     * @var stdClass|null 
     */
    private $data;

    /**
     * Called on postLoad Entity life cycle.
     * Because doctrine converts json objects to associative arrays instead of objects and we want objects, which is default php behaviour!
     * @see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#lifecycle-callbacks
     * 
     * @ORM\PostLoad
     */
    public function postLoad()
    {
        $this->data = json_decode(json_encode($this->data));
    }

    /**
     * @param \App\Entity\Location $origin
     * @param \App\Entity\Location $destination
     * @param string $data Json directions information
     */
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
    
    public function getData(): ?stdClass
    {
        return $this->data;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
}

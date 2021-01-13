<?php

namespace App\Entity;

use App\States\StateInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name:'savedstate')]
class SavedState implements EntityInterface
{

    #[ORM\Id]
    #[ORM\Column(type:'smallint', unique:true, options:['unsigned' => true])]
    private int $key;

    #[ORM\Column(type:'object')]
    private StateInterface $state;

    public function __construct(StateInterface $state)
    {
        $this->key = $state->getKey();
        $this->state = $state;
    }

    /** Get the full state object. */
    public function getState(): StateInterface
    {
        return $this->state;
    }

    /** Overwrite the full state object. */
    public function setState(StateInterface $state)
    {
        $this->key = $state->getKey();
        $this->state = $state;
    }
}

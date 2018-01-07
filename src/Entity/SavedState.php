<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\States\StateInterface;

/**
 * @ORM\Table(name="savedstate")
 * @ORM\Entity(repositoryClass="App\Repository\SavedStateRepository")
 */
class SavedState
{
    /**
     * @ORM\Id
     * @ORM\Column(type="smallint", unique=true, options={"unsigned":true})
     *
     * @var int
     */
    private $key;

    /**
     * @ORM\Column(type="object")
     *
     * @var StateInterface
     */
    private $state;

    /**
     * Note: never gets called by Doctrine ORM.
     *
     * @param int $key
     */
    public function __construct(StateInterface $state)
    {
        $this->key = $state->getKey();
        $this->state = $state;
    }

    /**
     * Get the full state object.
     *
     * @return StateInterface
     */
    public function getState(): StateInterface
    {
        return $this->state;
    }

    /**
     * Overwrite the full state object
     * Warning!: do not use numeric values for state names!
     *
     * @param stdClass StateInterface
     */
    public function setState(StateInterface $state)
    {
        $this->key = $state->getKey();
        $this->state = $state;
    }
}

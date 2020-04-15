<?php

namespace App\Entity;

use App\States\StateInterface;
use Doctrine\ORM\Mapping as ORM;

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
     */
    public function __construct(StateInterface $state)
    {
        $this->key = $state->getKey();
        $this->state = $state;
    }

    /**
     * Get the full state object.
     */
    public function getState(): StateInterface
    {
        return $this->state;
    }

    /**
     * Overwrite the full state object.
     */
    public function setState(StateInterface $state)
    {
        $this->key = $state->getKey();
        $this->state = $state;
    }
}

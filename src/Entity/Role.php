<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Role Entity for database.
 *
 * @ORM\Entity(repositoryClass="App\Repository\RoleRepository")
 * @ORM\Table(name="role")
 */
class Role
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="roles")
     */
    private $users;

    /**
     * @param string $role Unique name for the role
     */
    public function __construct(string $name)
    {
        $this->users = new ArrayCollection();
        $this->name = $name;
        parent::__construct($name);
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * Get the name for the role.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Overrides the parent getRole.
     *
     * @return string
     */
    public function getRole(): string
    {
        return $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}

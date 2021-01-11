<?php

namespace App\Entity;

use Stringable;
use Doctrine\Common\Collections\{Collection, ArrayCollection};
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass:'App\Repository\RoleRepository')]
#[ORM\Table(name:'role')]
final class Role implements Stringable
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:'integer')]
    private int $id;

    #[ORM\Column(type:'string', unique:true)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\ManyToMany(targetEntity:'App\Entity\User', mappedBy:'roles')]
    private Collection $users;

    /** @param string $role Unique name for the role */
    public function __construct(string $name)
    {
        $this->users = new ArrayCollection();
        $this->name = $name;
    }

    /** @return Collection A collection of User entities */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRole(): string
    {
        return $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->name;
    }
}

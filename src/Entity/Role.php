<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Role\Role as BaseRole;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoleRepository")
 * @ORM\Table(name="role")
 */
class Role extends BaseRole
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     */
    protected $role;
    
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="roles")
     */
    protected $users;
    
    /**
     * @param string $role Unique name for the role
     */
    public function __construct(string $role)
    {
        $this->users = new ArrayCollection();
        $this->role = $role;
        parent::__construct($role);
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    /**
     * @return ArrayCollection|User[]
     */
    public function getUsers(): ArrayCollection
    {
        return $this->users;
    }
}

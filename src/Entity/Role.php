<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\Column(type="string")
     */
    protected $role;
    
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="roles")
     */
    protected $users;
    
    public function __construct(string $role)
    {
        $this->users = new ArrayCollection();
        parent::__construct($role);
    }
    
    /**
     * @return ArrayCollection|User[]
     */
    public function getUsers(): ArrayCollection
    {
        return $this->users;
    }
}

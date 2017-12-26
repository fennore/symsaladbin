<?php

namespace App\Entity;

use \Serializable;
use \DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User Entity
 * 
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="username", message="Username already taken")
 */
class User implements AdvancedUserInterface, Serializable, EquatableInterface
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true, length=32)
     * @Assert\NotBlank()
     */
    protected $username;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    protected $plainPassword;
    
    /**
     * @ORM\Column(type="string", length=256)
     */
    protected $password;
    
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role", inversedBy="users")
     * @ORM\JoinTable(name="user_role")
     * @var Collection
     */
    protected $roles;

    /**
     * @ORM\Column(type="smallint", options={"unsigned":true})
     */
    protected $status;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $created;

    public function __construct(string $username, ?string $password, array $roles = array(), int $status = 1)
    {
        if ('' === $username || null === $username) {
            throw new \InvalidArgumentException('The username cannot be empty.');
        }

        $datetime = new DateTime();
        $this->username = $username;
        $this->password = $password;
        $this->roles = new ArrayCollection($roles);
        $this->status = $status;
        $this->created = $datetime->getTimestamp();
    }
    
    public function setUsername($username): void
    {
        $this->username = $username;
    }
    
    public function setPassword($password): void
    {
        $this->password = $password;
    }
    
    public function setRoles(array $roles): void
    {
        $this->roles = new ArrayCollection($roles);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getSalt()
    {
        return null;
    }

    /**
     * @return ArrayCollection|Role[]
     */
    public function getRoles(): array
    {
        return $this->roles->toArray();
    }

    public function eraseCredentials(): void
    {
    }

    public function isEqualTo(UserInterface $user): bool
    {
        return $this->id === $user->getId() && $user instanceof User;
    }

    public function isAccountNonExpired(): bool
    {
        return true;
    }

    public function isAccountNonLocked(): bool
    {
        return true;
    }

    public function isCredentialsNonExpired(): bool
    {
        return true;
    }

    public function isEnabled(): bool
    {
        return $this->status > 0;
    }

    public function serialize(): string
    {
        return \serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->status
            // see section on salt below
            // $this->salt,
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password,
            $this->status
            // see section on salt below
            // $this->salt
        ) = \unserialize($serialized);
    }

}

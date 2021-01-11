<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name:'user')]
#[ORM\Entity(repositoryClass:'App\Repository\UserRepository')]
#[UniqueEntity(fields:'username', message:'Username already taken')]
final class User implements UserInterface, Serializable, EquatableInterface
{
    
    #[ORM\Column(type:'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:'AUTO')]
    private int $id;

    
    #[ORM\Column(type:'string', unique:true, length:32)]
    #[Assert\NotBlank]
    private string $username;

    
    #[Assert\NotBlank]
    #[Assert\Length(max:4096)]
    private string $plainPassword;

    
    #[ORM\Column(type:'string', length:256)]
    private string $password;

    
    #[ORM\ManyToMany(targetEntity:'App\Entity\Role', inversedBy:'users')]
    #[ORM\JoinTable(name:'user_role')]
    private Collection $roles;

    
    #[ORM\Column(type:'smallint', options:['unsigned' => true])]
    private int $status;

    
    #[ORM\Column(type:'integer', options:['unsigned' => true])]
    private int $created;

    public function __construct(string $username, ?string $password, array $roles = [], int $status = 1)
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
     * @return string[] An array of Role names related to the user
     */
    public function getRoles(): array
    {
        return $this->roles->map(fn (Role $role) => (string) $role)->toArray();
    }

    public function eraseCredentials(): void
    {
    }

    public function isEqualTo(UserInterface $user): bool
    {
        return $this->id === $user->getId() && $user instanceof self;
    }

    public function isEnabled(): bool
    {
        return $this->status > 0;
    }

    public function serialize(): string
    {
        return \serialize([
            $this->id,
            $this->username,
            $this->password,
            $this->status,
        ]);
    }

    public function unserialize($serialized): void
    {
        list(
            $this->id,
            $this->username,
            $this->password,
            $this->status
        ) = \unserialize($serialized);
    }
}

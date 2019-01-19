<?php

namespace App\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"})
 * @Gedmo\SoftDeleteable(fieldName="active")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active = true;

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @ORM\Column(type="string", length=80)
     * @Assert\NotBlank(message="Vous devez entrer un prénom.")
     * @Assert\Length(max="50", maxMessage="Le prénom ne doit pas dépasser {{ limit }} caractères.")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez entrer un nom.")
     * @Assert\Length(max="50", maxMessage="Le nom ne doit pas dépasser {{ limit }} caractères.")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="Veuillez entrer une adresse email valide.")
     * @Assert\NotBlank(message="Vous devez entrer une adresse email.")
     * @Assert\Length(max="80", maxMessage="L'adresse email ne doit pas dépasser {{ limit }} caractères.")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank(message="Vous devez entrer un mot de passe.")
     */
    private $password;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * Les réunions dont User est organisateur.
     * @ORM\OneToMany(targetEntity="App\Entity\Unavailability", mappedBy="organiser")
     */
    private $unavailabilities;

    /**
     * Les réunions auxquelles User a été invité.
     * @ORM\ManyToMany(targetEntity="App\Entity\Unavailability", mappedBy="guests")
     */
    private $invitations;

    public function __construct()
    {
        $this->unavailabilities = new ArrayCollection();
        $this->invitations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole($role): bool
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
            return true;
        }
        return false;
    }

    public function hasRole($role): bool
    {
        if (in_array($role, $this->roles)) {
            return true;
        }
        return false;
    }

    /**
     * @return Collection|Unavailability[]
     */
    public function getUnavailabilities(): Collection
    {
        return $this->unavailabilities;
    }

    /**
     * @param Unavailability $unavailability
     * @return User
     */
    public function addUnavailability(Unavailability $unavailability): self
    {
        if (!$this->unavailabilities->contains($unavailability)) {
            $this->unavailabilities[] = $unavailability;
            $unavailability->setOrganiser($this);
        }

        return $this;
    }

    /**
     * @param Unavailability $unavailability
     * @return User
     */
    public function removeUnavailability(Unavailability $unavailability): self
    {
        if ($this->unavailabilities->contains($unavailability)) {
            $this->unavailabilities->removeElement($unavailability);
            // set the owning side to null (unless already changed)
            if ($unavailability->getOrganiser() === $this) {
                $unavailability->setOrganiser(null);
            }
        }

        return $this;
    }

    public function hasUpcomingUnavailabilities() : bool
    {
        $now = new \DateTime();
        foreach ($this->unavailabilities as $unavailability) {
            if ($now < $unavailability->getStartDate()) {
                return true;
            }
        }
        return false;
    }

    public function hasUpcomingInvitations() : bool
    {
        $now = new \DateTime();
        foreach ($this->getInvitations() as $invitation)
            if ($now < $invitation->getStartDate()) {
                return true;
            }
        return false;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return mixed
     */
    public function getInvitations()
    {
        return $this->invitations;
    }

    /**
     * @param mixed $invitations
     */
    public function setInvitations($invitations): void
    {
        $this->invitations = $invitations;
    }

    /**
     * String representation of object
     * @link https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->email,
            $this->password,
            $this->roles
        ]);
    }

    /**
     * Constructs the object
     * @link https://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password,
            $this->roles) = unserialize($serialized);
    }
}

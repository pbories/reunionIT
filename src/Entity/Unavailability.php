<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Availability as UnavailabilityAssert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UnavailabilityRepository")
 * @UnavailabilityAssert
 */
class Unavailability
{
    const   REUNION = 0,
            AUTRE = 1;

    const   DAY_START = '08:00',
            DAY_END = '20:00';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="datetime")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endDate;

    /**
     * @ORM\Column(type="string", length=80)
     * @Assert\NotBlank(message="Vous devez saisir un objet")
     * @Assert\Length(max="80", maxMessage="L'objet de la réunion ne doit pas dépasser {{ limit }} caractères.")
     */
    private $object;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotNull()
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="unavailabilities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organiser;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Room", inversedBy="unavailabilities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $room;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="invitations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $guests;

    public function __construct()
    {
        $this->guests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function setObject(string $object): self
    {
        $this->object = $object;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getOrganiser(): ?User
    {
        return $this->organiser;
    }

    public function setOrganiser(?User $organiser): self
    {
        $this->organiser = $organiser;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): self
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Vérifie qu'un utilisateur est bien
     * l'organisateur de la réunion.
     * @param User|null $user
     * @return bool
     */
    public function isOrganiser(?User $user = null): bool
    {
        return $user && $this->getOrganiser()->getId() === $user->getId();
    }

    public function isNotPast()
    {
        return $this->getStartDate() > new \DateTime();
    }

    /**
     * @return Collection|User[]
     */
    public function getGuests(): Collection
    {
        return $this->guests;
    }

    public function isGuest(User $user) : bool
    {
        return $this->guests->contains($user);
    }

    public function addGuest(User $guest): self
    {
        if (!$this->guests->contains($guest)) {
            $this->guests[] = $guest;
        }

        return $this;
    }

    public function removeGuest(User $guest): self
    {
        if ($this->guests->contains($guest)) {
            $this->guests->removeElement($guest);
        }

        return $this;
    }
}

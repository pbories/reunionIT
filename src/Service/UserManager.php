<?php

namespace App\Service;


use App\Entity\Unavailability;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserManager
{
    private $entityManager;

    /**
     * UnavailabilityManager constructor.
     * @param $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Retire un utilisateur de la liste des invités aux réunions à venir.
     * @param User $user
     */
    public function removeUserFromUpcomingUnavailabilitiesGuests(User $user)
    {
        $unavailabilityRepository = $this->entityManager->getRepository(Unavailability::class);
        $entityManager = $this->entityManager;

        $unavailabilities = $unavailabilityRepository->findUpcomingUnavailabilitiesByGuest($user);

        foreach ($unavailabilities as $unavailability) {
            $unavailability->removeGuest($user);
            $entityManager->persist($unavailability);
        }
        $entityManager->flush();
    }

    /**
     * Désactive un utilisateur au premier appel.
     * Supprime un utilisateur de la BDD au second appel.
     * @param User $user
     */
    public function removeUserFromDatabase(User $user)
    {
        $entityManager = $this->entityManager;
        $entityManager->remove($user);
        $entityManager->flush();
    }



}
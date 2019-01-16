<?php

namespace App\Validator;

use App\Repository\UnavailabilityRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AvailabilityValidator extends ConstraintValidator
{
    private $unavailabilityRepository;

    /**
     * AvailabilityValidator constructor.
     * @param UnavailabilityRepository $unavailabilityRepository
     */
    public function __construct(UnavailabilityRepository $unavailabilityRepository)
    {
        $this->unavailabilityRepository = $unavailabilityRepository;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if ($this->availability($value)) {
            $this->context->buildViolation($constraint->availabilityMessage)
                ->addViolation();
        }

        if ($this->endAfterStart($value)) {
            $this->context->buildViolation($constraint->endAfterStartMessage)
                ->addViolation();
        }

        if ($this->pastDates($value)) {
            $this->context->buildViolation($constraint->pastDatesMessage)
                ->addViolation();
        }

        if ($this->weekEndDates($value)) {
            $this->context->buildViolation($constraint->weekEndDatesMessage)
                ->addViolation();
        }
        if ($this->tooManyGuests($value)) {
            $this->context->buildViolation($constraint->tooManyGuestsMessage)
                ->addViolation();
        }
    }

    public function availability($value)
    {
        $unavailabilities = $this->unavailabilityRepository->findUpcomingUnavailabilitiesByRoom($value->getRoom());
        foreach ($unavailabilities as $unavailability) {
            if($value->getId() === $unavailability->getId())
                continue;
            if ($unavailability->getStartDate() < $value->getStartDate()
                && $value->getStartDate() < $unavailability->getEndDate()) {
                return true;
            }
            if ($unavailability->getStartDate() < $value->getEndDate()
                && $value->getEndDate() < $unavailability->getEndDate()) {
                return true;
            }
            if ($value->getStartDate() < $unavailability->getStartDate()
                && $unavailability->getStartDate() < $value->getEndDate()) {
                return true;
            }
            if ($value->getStartDate() < $unavailability->getEndDate()
                && $unavailability->getStartDate() < $value->getEndDate()) {
                return true;
            }
        }
        // return false;
    }

    public function endAfterStart($value)
    {
        return ($value->getEndDate() < $value->getStartDate());
    }

    public function pastDates($value)
    {
        $now = new \DateTime();
        return ($value->getStartDate() < $now);
    }

    public function weekEndDates($value)
    {
        return $this->isWeekEndDate($value->getStartDate()) || $this->isWeekEndDate($value->getEndDate());
    }

    public function isWeekEndDate(\DateTime $date) : bool
    {
        $day = $date->format('w');
        // Retourne true si le jour est un samedi ou un dimanche
        return $day == 0 || $day == 6;
    }

    // La capacité de la salle doit être supérieure ou égale au nombre d'invités + 1 (l'organisateur).
    public function tooManyGuests($value) {
        return ($value->getRoom()->getCapacity() < count($value->getGuests()) + 1);
    }
}

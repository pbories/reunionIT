<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Availability extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $availabilityMessage = 'La salle n\'est pas disponible pour l\'ensemble de cette période.';
    public $endAfterStartMessage = 'Une réunion ne peut pas finir avant d\'avoir commencé. Malheureusement.';
    public $pastDatesMessage = 'La date ou l\'heure de début est passée. Trop tard.';
    public $weekEndDatesMessage = 'Impossible de réserver une salle le samedi et/ou le dimanche.';

    public function validatedBy()
    {
        return \get_class($this).'Validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

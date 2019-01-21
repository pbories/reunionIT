<?php
/**
 * Created by PhpStorm.
 * User: Etudiant
 * Date: 07/01/2019
 * Time: 14:44
 */

namespace App\Form;


use App\Entity\Unavailability;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class UnavailabilityAdminType extends UnavailabilityType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $users = $this->userRepository->findActiveUsers();

        parent::buildForm($builder, $options);
        
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type de réservation',
                'choices' => [
                    'Réunion' => Unavailability::REUNION,
                    'Autre' => Unavailability::AUTRE
                ],
                'expanded' => false,
                'multiple' => false
            ])
            ->add('organiser', EntityType::class, [
                'label' => 'Organisateur',
                'class' => User::class,
                'choices' => $users,
                'choice_label' => function(User $user) {return $user->getFirstName().' '.$user->getLastName();},
                'expanded' => false,
                'multiple' => false,
            ]);

    }

}
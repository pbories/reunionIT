<?php

namespace App\Form;

use App\Entity\Room;
use App\Entity\Unavailability;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UnavailabilityType extends AbstractType
{
    protected $userRepository;

    /**
     * UnavailabilityType constructor.
     * @param $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $users = $this->userRepository->findActiveUsers();
        $builder
            ->add('startDate', DateTimeType::class, [
                'label' => 'Début',
                'date_format' => 'dd/MM/yyyy HH:ii',
                'widget' => 'single_text',
            ])
            ->add('endDate', DateTimeType::class, [
                'label' => 'Fin',
                'date_format' => 'dd/MM/yyyy HH:ii',
                'widget' => 'single_text'
            ])
            ->add('guests', EntityType::class, [
                'label' => 'Invités',
                'class' => User::class,
                'choices' => $users,
                'choice_label' => function(User $user) {return $user->getFirstName().' '.$user->getLastName();},
                'multiple' => true
            ])
            ->add('object', TextType::class, [
                'label' => 'Objet'
            ])
            ->add('room', EntityType::class, [
                'label' => 'Salle',
                'class' => Room::class,
                'choice_label' => 'name'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Unavailability::class,
        ]);
    }
}

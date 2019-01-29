<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;

class UserAdminType extends UserType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN',

                    'Salarié' => 'ROLE_EMPLOYEE',

                    'Invité' => 'ROLE_GUEST'
                ],
                'label' => 'Statut',
                'expanded' => true,
                'multiple' => false,
            ]);

        $builder
            ->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesAsArray) {
                    // transform the array to a string
                    // (transforms the original value into a format that'll be used to render the field)
                    return implode(', ', $rolesAsArray);
                },
                function ($rolesAsString) {
                    // transform the string back to an array
                    // (transforms the submitted value back into the format you'll use in your code)
                    return explode(', ', $rolesAsString);
                }
            ));
    }
}

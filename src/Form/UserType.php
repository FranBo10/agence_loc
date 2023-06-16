<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [                
                'choices'  => [
                    'admin' => 'ROLE_ADMIN',
                    'user' => 'ROLE_USER'
                ],
                'expanded' => true,
                'multiple' => true,

            ])  
            ->add('password')
            ->add('pseudo')
            ->add('nom')
            ->add('prenom')
            ->add('civilite', ChoiceType::class, [                
                'choices'  => [
                    'Femme' => 'femme',
                    'Homme' => 'homme'
                ],

            ])
            // ->add('date_enregistrement')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

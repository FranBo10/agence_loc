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
            ->add('email', null, [
                'label' => "E-Mail",
                'label_attr' => ['class' => 'text-white bg-dark p-1 rounded'],
            ])
            ->add('roles', ChoiceType::class, [                
                'choices'  => [
                    'admin' => 'ROLE_ADMIN',
                    'user' => 'ROLE_USER'
                ],
                'expanded' => true,
                'multiple' => true,

            ])  
            ->add('password', null, [
                'label' => "Mot de passe",
                'label_attr' => ['class' => 'text-white bg-dark p-1 rounded'],
            ])
            ->add('pseudo', null, [
                'label' => "Pseudo",
                'label_attr' => ['class' => 'text-white bg-dark p-1 rounded'],
            ])
            ->add('nom', null, [
                'label' => "Nom",
                'label_attr' => ['class' => 'text-white bg-dark p-1 rounded'],
            ])
            ->add('prenom', null, [
                'label' => "Prénom",
                'label_attr' => ['class' => 'text-white bg-dark p-1 rounded'],
            ])
            ->add('civilite', ChoiceType::class, [
                'label' => "Prénom",
                'label_attr' => ['class' => 'text-white bg-dark p-1 rounded'],             
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

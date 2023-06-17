<?php

namespace App\Form;

use App\Entity\Vehicule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('titre')
            ->add('marque', null, [
                'label' => "Marque",
                'label_attr' => ['class' => 'text-white bg-dark p-1 rounded'],
            ])
            ->add('modele', null, [
                'label' => "Modèle",
                'label_attr' => ['class' => 'text-white bg-dark p-1 rounded'],
            ])
            ->add('description', null, [
                'label' => "Description",
                'label_attr' => ['class' => 'text-white bg-dark p-1 rounded'],
            ])
            ->add('photo', null, [
                'label' => "Photo",
                'label_attr' => ['class' => 'text-white bg-dark p-1 rounded'],
            ])
            ->add('prix_journalier', null, [
                'label' => "Prix journalier",
                'label_attr' => ['class' => 'text-white bg-dark p-1 rounded'],
            ])
            // ->add('date_enregistrement')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
}

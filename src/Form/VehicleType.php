<?php

namespace App\Form;

use App\Entity\Vehicle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class VehicleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $optons): void
    {
        $builder
            ->add('brand', TextType::class, [
                'label' => 'Marque',
                'attr' => ['placeholder'=> 'Renault'],
            ])
            ->add('model', TextType::class, [
                'label' => 'Modèle',
                'attr' => ['placeholder' => 'Clio'],
            ])
            ->add('color', TextType::class, [
                'label' => 'Couleur',
                'attr' => ['placeholder' => 'Noir'],
            ])
            ->add('licensePlate', TextType::class, [
                'label' => 'Plaque immatriculation',
                'attr' => ['placeholder' => 'AB-123-CD'],
            ])
            ->add('seats', IntegerType::class, [
                'label' => 'Nombre de place',
                'attr' => ['min' => 1, 'max' => 9],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de véhicule',
                'choices' => [
                    'Citadine' => 'citadine',
                    'Berline' => 'berline',
                    'SUV' => 'suv',
                    'Break' => 'break',
                    'Utilitaire' => 'utilitaire',
                    'Autre' => 'other',
                ],
            ])
            ->add('isElectric', CheckboxType::class, [
                'label' => 'Véhicule électrique ?',
                'required' => false,
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicle::class,
        ]);
    }
}
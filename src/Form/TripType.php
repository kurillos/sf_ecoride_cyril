<?php

namespace App\Form;

use App\Entity\Trip;
use App\Entity\User;
use App\Entity\Vehicle;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use SYmfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('departureLocation', TextType::class, [
                'label' => 'Lieu de départ',
                'attr' => ['placeholder' => 'Ex: Paris'],
            ])
            ->add('destinationLocation', TextType::class, [
                'label' => "Lieu d'arriver",
                'attr' => ['placeholder' => 'Ex: Lyon'],
            ])
            ->add('departureTime', DateTimeType::class, [
                'label' => 'Date et heure de départ',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['class' => 'js-datepicker'],
            ])
            ->add('arrivalTime', DateTimeType::class, [
                'label' => "Date et ehure d'arriver",
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['class' => 'js-datepicker'],
            ])
            ->add('availableSeats', IntegerType::class, [
                'label' => 'Nombre de place disponibles',
                'attr' => ['min' => 1],
            ])
            ->add('pricePerSeat', MoneyType::class, [
                'label' => 'Prix par place',
                'currency' => 'EUR',
                'attr' => ['placeholder' => 'Ex: 15.00'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du voyage (facultatif)',
                'required' => false,
                'attr' => ['rows' => 5, 'placeholder' => 'Ex: arrêt à mi-chemin pour pause cigarette, type de musique'],
            ])
            ->add('isSmokingAllowed', CheckboxType::class, [
                'label' => 'Fumeur autorisé ?',
                'required' => true,
            ])
            ->add('areAnimalsAllowed', CheckboxType::class, [
                'label' => 'Animaux autorisés ?',
                'required' => true,
            ])
            ->add('status')
            ->add('driver', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('vehicle', EntityType::class, [
                'class' => Vehicle::class,
                'choice_label' => 'model',
                'placeholder' => 'Choisissez votre véhicule.',
                'label' => 'Véhicule utilisé',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Trip;
use App\Entity\User;
use App\Entity\Vehicle;
use DateTime;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
                'required' => false,
            ])
            ->add('areAnimalsAllowed', CheckboxType::class, [
                'label' => 'Animaux autorisés ?',
                'required' => false,
            ])
            
            ->add('vehicle', EntityType::class, [
                'class' => Vehicle::class,
                'choice_label' => function (Vehicle $vehicle) {
                    return $vehicle->getBrand() . ' ' . $vehicle->getModel() . ' (' . $vehicle->getLicensePlate() . ')';
                },
                'placeholder' => 'Choisissez votre véhicule.',
                'label' => 'Véhicule utilisé',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    if (isset($options['current_user']) && $options['current_user'] instanceof User) {
                        return $er->createQueryBuilder('v')
                            ->where('v.owner = :user')
                            ->setParameter('user', $options['current_user'])
                            ->orderBy('v.brand', 'ASC');
                    }

                    return $er->createQueryBuilder('v');
                },
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
            'current_user' => null,
        ]);

        $resolver->setAllowedTypes('current_user', [User::class, 'null']);
    }
}

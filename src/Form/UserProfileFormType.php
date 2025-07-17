<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\UserPreference;
use App\Form\UserPreferenceType;

class UserProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'Entrez votre email',
                ],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'prénom',
                'attr' => [
                    'placeholder' => 'Entrez votre prénom',
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'nom',
                'attr' => [
                    'placeholder' => 'Entrez votre nom',
                ],
            ])
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudonyme',
                'attr' => [
                    'placeholder' => 'Entrez votre pseudonyme',
                ],
            ])
            ->add('userPreference', UserPreferenceType::class, [
                'label' => 'false',
            ])
            ->add('desiredRole', ChoiceType::class, [
                'label' => 'Êtes-vous passager, chauffeur ou les deux ?',
                'choices' => [
                    'Passager seulement' => 'passenger',
                    'Chauffeur seulement' => 'driver',
                    'Les deux'=> 'both',
                ],
                'expanded' => false,
                'multiple' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

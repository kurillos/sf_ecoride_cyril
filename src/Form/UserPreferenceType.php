<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class UserPreferenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isSmoker', CheckboxType::class, [
                'label' => 'Je suis fumeur',
                'required' => false,
            ])
            ->add('acceptsAnimals', CheckboxType::class, [
                'label' => 'J\'accepte les animaux dans mes covoiturages',
                'required' => false,
            ])
            ->add('additionalInfo', TextareaType::class, [
                'label' => 'Informations supplémentaires (max 500 caractères)',
                'required' => false,
                'attr' => ['rows' => 5],
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
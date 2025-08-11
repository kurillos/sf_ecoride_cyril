<?php

namespace App\Form;

use App\Entity\Rating;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface as FormFormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class RatingType extends AbstractType
{
    public function buildForm(FormFormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rating', HiddenType::class, [
                'required' => true,
                'label' => false
            ])

            ->add('comment', TextareaType::class, [
                'label' => 'Commentaire',
                'required' => false,
                'attr' => ['placeholder' => 'Votre commentaire...'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rating::class,
        ]);
    }
}
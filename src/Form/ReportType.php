<?php

namespace App\Form;

use App\Entity\Report;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reason', TextareaType::class, [
                'label' => 'Motif du signalement',
                'attr' => [
                    'placeholder' => 'Veuillez décrire le motif de votre signalement ici...',
                    'rows' => 5,
                    'class' => 'form-control rounded-md border-gray-300 focus:border-indigo-300 focus-ring focus-ring-indigo-200 focus:ring-opacity-50'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Report::class,
        ]);
    }
}
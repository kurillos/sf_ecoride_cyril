<?php

namespace App\Form;

use App\Entity\Report;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\SecurityBundle\Security;

class ReportType extends AbstractType
{

    private $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $user = $this->security->getUser();
        $userEmail = null;

        if ($user && method_exists($user, 'getEmail')) {
            $userEmail = $user->getEmail();
        }

        $builder
            ->add('reason', TextareaType::class, [
                'label' => 'Motif du signalement',
                'attr' => [
                    'placeholder' => 'Veuillez décrire le motif de votre signalement ici...',
                    'rows' => 5,
                    'class' => 'form-control rounded-md border-gray-300 focus:border-indigo-300 focus-ring focus-ring-indigo-200 focus:ring-opacity-50'
                ],
            ])
            ->add('contactEmail', EmailType::class, [
                'label' => 'Votre adresse e-mail',
                'required' => true,
                'data' => $userEmail,
                'attr' => [
                    'placeholder' => 'Pour que nous puissions vous recontacter',
                    'class' => 'form-control rounded-md border-gray-300 focus:border-indigo-300 focus-ring-indigo-200 focus:ring-oacity-50'
                ]
            ])
            ->add('contactPhone', TextType::class, [
                'label' => 'Votre numéro de téléphone',
                'required' => false,
                'attr' => [
                    'placeholder' => 'ex: 06 00 00 00 00',
                    'class' => 'form-control rounded-md border-gray-300 focus-border-indigo-300 focus-ring focus-ring-indigo-200 focus:ring-opacity-50'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Report::class,
        ]);
    }
}
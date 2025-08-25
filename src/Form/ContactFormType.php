<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre prénom.']),
                    new Length(['min' => 3, 'max' => 100, 'minMessage' => 'Votre prénom doit contenir au moins {{ limit }} caractères.'])
                ],
                'attr' => [
                    'minlength' => 3,
                    'required' => true
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre nom.']),
                    new Length(['min' => 3, 'max' => 100, 'minMessage' => 'Votre nom doit contenir au moins {{ limit }} caractères.'])
                ],
                'attr' => [
                    'minlength' => 3,
                    'required' => true
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre adresse email.']),
                    new Email(['message' => 'L\'adresse email "{{ value }}" n\'est pas valide.'])
                ],
                'attr' => [
                    'required' => true
                ]
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Numéro de téléphone (Optionnel)',
                'required' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^0[1-9]([ .-]?[0-9]{2}){4}$/',
                        'message' => 'Le numéro de téléphone n\'est pas dans un format valide.'
                    ])
                ],
                'attr' => [
                    'pattern' => '^0[1-9]([ .-]?[0-9]{2}){4}$'
                ]
            ])
            ->add('subject', TextType::class, [
                'label' => 'Objet du message',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer l\'objet de votre message.']),
                    new Length(['min' => 5, 'max' => 255, 'minMessage' => 'L\'objet doit contenir au moins {{ limit }} caractères.'])
                ],
                'attr' => [
                    'minlength' => 5,
                    'required' => true
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
                'attr' => [
                    'rows' => 6,
                    'minlength' => 10,
                    'required' => true
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez écrire votre message.']),
                    new Length(['min' => 10, 'minMessage' => 'Votre message doit contenir au moins {{ limit }} caractères.'])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([

        ]);
    }
}
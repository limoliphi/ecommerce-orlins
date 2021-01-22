<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegisterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Votre prénom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'S\'il vous plaît, indiquez votre prénom',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre prénom doit faire au minimum {{ limit }} caractères',
                        'max' => 30,
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Veuillez saisir votre prénom'
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Votre nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'S\'il vous plaît, indiquez votre nom',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre nom doit faire au minimum {{ limit }} caractères',
                        'max' => 30,
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Veuillez saisir votre nom'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre adresse email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'S\'il vous plaît, indiquez votre email',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre email doit faire au minimum {{ limit }} caractères',
                        'max' => 60,
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Veuillez saisir votre adresse email'
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Attention, le mot de passage et la confirmation doivent être identiques',
                'label' => 'Confirmez votre mot de passe',
                'required' => true,
                'first_options' => ['label' => 'Veuillez saisir votre mot de passage'],
                'second_options' => ['label' => 'Veuillez confirmer votre mot de passage'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

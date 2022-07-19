<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class newPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('plainPassword', RepeatedType::class, [
            'type'=>PasswordType::class,
            'invalid_message' => 'Les mots de passe ne correspondent pas.',
            'first_options'  => ['label' => 'Mot de passe :'],
            'second_options' => ['label' => 'Confirmer le mot de passe :'],
            // instead of being set onto the object directly,
            // this is read and encoded in the controller
            'mapped' => false,
            'attr' => ['autocomplete' => 'new-password'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer un mot de passe',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ]),
                new Regex([
                    'message'=>'Votre mot de passe doit comporter au moins une lettre en majuscule, une en minuscule, un caractère spécial et un chiffre',
                    'pattern'=>'/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,16}$/'
                ])
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

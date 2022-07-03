<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
        ->add('name', TextType::class, [
            'required'=>false,
            'empty_data'=>''
        ])
        ->add('surname', TextType::class, [
            'required'=>false,
            'empty_data'=>''
        ])
        ->add('mail', EmailType::class, [
            'required'=>true,
            'empty_data'=>''
        ])
        ->add('description', TextareaType::class, [
            'required'=>false
        ])
        ->add('ToS')
        ->add('disponible')
        ->add('private')
        ->add('avatar', FileType::class, [
            'data_class'=>null,
            'required' => false,
            'constraints' => [
                new Image()
            ]
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

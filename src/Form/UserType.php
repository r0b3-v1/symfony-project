<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
        ->add('name', TextType::class, [
            'required'=>false,
            'empty_data'=>'',
            'constraints'=> [
                    new Regex([
                        'message'=>'Ce nom n\'est pas valide',
                        'pattern'=>'/^[^<>]*$/'
                    ])
                ]
        ])
        ->add('surname', TextType::class, [
            'required'=>false,
            'empty_data'=>'',
            'constraints'=> [
                    new Regex([
                        'message'=>'Ce nom n\'est pas valide',
                        'pattern'=>'/^[^<>]*$/'
                    ])
                ]
        ])
        ->add('mail', EmailType::class, [
            'required'=>true,
            'empty_data'=>'',
            'constraints'=>[
                new Regex([
                    'message' => 'Votre adresse mail est invalide',
                    'pattern' => '/^([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22))*\x40([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d))*$/'
                ])
            ]
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

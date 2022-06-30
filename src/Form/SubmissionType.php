<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SubmissionType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('title')
            ->add('image', FileType::class, [
                'required' => true,
                'constraints' => [
                    new Image()
                ]
            ])
            ->add('description', TextareaType::class)
            ->add('tags', TextareaType::class)
            ->add('category', EntityType::class,[
                'class'=>Category::class,
                'choice_label'=>'name',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            //'data_class' => Submission::class,
        ]);
    }
}

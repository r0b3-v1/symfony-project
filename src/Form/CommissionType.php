<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CommissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required'=>true,
            ])
            ->add('description', TextareaType::class, [
                'required'=>true,
            ])
            ->add('deadline', DateType::class, [
                'data' => new \DateTime()
            ])
            ->add('nodeadline', CheckboxType::class, [
                'required'=>false
            ])
            ->add('category', EntityType::class, [
                'class'=>Category::class,
                'choice_label'=>'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // 'data_class' => Commission::class,
        ]);
    }
}

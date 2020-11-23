<?php

namespace App\Form;

use App\Entity\Quack;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class, [
                'attr' => ['class' => 'content-width',
                    'placeholder' => ' Quack a new quack!'],
            ])
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('tags', SearchForm::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Quack::class,
        ]);
    }
}

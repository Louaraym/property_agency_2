<?php

namespace App\Form;

use App\Entity\Option;
use App\Entity\PropertySearch;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertySearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('maxPrice', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Budget maximal'
                ]
            ])
            ->add('minSurface',IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Surface minimale'
                ]
            ])
            ->add('options', EntityType::class, [
                'label' => false,
                'class' => Option::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
            ])
            ->add('address', null, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Adresse de recherche',
                ]
            ])
            ->add('distance', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'placeholder' => 'choisir une distance',
                'choices' => [
                    '15 km' => 15,
                    '25 km' => 25,
                    '50 km' => 50,
                    '100 km' => 100,
                    '150 km' => 150,
                    '250 km' => 250,
                    '500 km' => 500,
                ]
            ])
            ->add('lat', HiddenType::class)
            ->add('lng', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PropertySearch::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix(): ?string
    {
        return '';
    }
}

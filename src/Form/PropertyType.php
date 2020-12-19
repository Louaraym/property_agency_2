<?php

namespace App\Form;

use App\Entity\Option;
use App\Entity\Property;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('surface')
            ->add('room_number', IntegerType::class)
            ->add('bedroom_number',IntegerType::class)
            ->add('floor', IntegerType::class)
            ->add('price', IntegerType::class)
            ->add('heat', ChoiceType::class, ['choices' => $this->getChoices(),])
            ->add('options', EntityType::class, [
                'class' => Option::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
            ])
          ->add('pictureFiles',  FileType::class, [
                'required' => false,
                'multiple' => true,
            ])
            ->add('city', TextType::class)
            ->add('address', TextType::class)
            ->add('postal_code', TextType::class)
            ->add('lat', HiddenType::class)
            ->add('lng', HiddenType::class)
            ->add('sold',  CheckboxType::class, ['required' => false,])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
            'translation_domain' => 'forms',
        ]);
    }

    private function getChoices(): array
    {
        $choices = Property::HEAT;
        $outPut = [];

        foreach ($choices as $k => $v){
            $outPut[$v] = $k;
        }

        return $outPut;
    }
}

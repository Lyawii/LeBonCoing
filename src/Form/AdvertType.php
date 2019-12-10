<?php

namespace App\Form;

use App\Entity\Advert;
use App\Entity\Category;
use App\Entity\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('price')
            ->add('category', ChoiceType::class, [
                'choices' => $this->getChoices()
            ])
            ->add('city')
            ->add('adress')
            ->add('postalcode')
            ->add('images', FileType::class, [
                'label' => 'Image',
                'data_class' => null,
                'multiple' => true,
                'attr' => [
                    'accept' => 'image/png, image/jpeg',
                    'multiple' => true
                ]
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Advert::class,
        ]);
    }

    private function getChoices()
    {
        $choices = Category::CATEGORY;
        $output = [];
        foreach ($choices as $k => $v)
        {
            $output[$v] = $k;
        }
        return $output;
    }
}
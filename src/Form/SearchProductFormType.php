<?php

namespace App\Form;

use App\Classe\SearchProduct;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchProductFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('productName', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Votre recherche...',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('productCategories', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Category::class,
                'multiple' => true,
                'expanded' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        //method Get pour que les utilisateurs puissent partager des URL configurées
        //désactivation du csrf car juste formulaire de recherche
        $resolver->setDefaults([
            'data_class' => SearchProduct::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    //retourne URL propre
    public function getBlockPrefix()
    {
        return '';
    }
}
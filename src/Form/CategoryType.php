<?php

namespace App\Form;

use App\Entity\Pole;
use App\Entity\Category;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CategoryType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration("Nom de la catégorie", "Entrez le nom de la catégorie ici"))
            ->add('pole', EntityType::class, [
                'class' => Pole::class,
                'choice_label' => function($pole){
                    return $pole->getTitle();
    
                }
    
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}

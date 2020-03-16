<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\SubCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SubCategoryType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title', TextType::class, $this->getConfiguration("Nom de la sous-catégorie", "Entrez le nom de la sous-catégorie ici"))
        ->add('category', EntityType::class, [
            'class' => Category::class,
            'label' => "Catégorie",
            'choice_label' => function($category){
                return $category->getTitle();

            }

        ])
        ->add('author', EntityType::class, [
            'class' => User::class,
            'label' => "Responsable de la mise à jour",
            'choice_label' => function($user){
                return $user->getFullname();

            }

        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SubCategory::class,
        ]);
    }
}

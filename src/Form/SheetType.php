<?php

namespace App\Form;

use App\Entity\Sheet;
use App\Entity\Category;
use App\Form\HeaderType;
use App\Form\SectionType;
use App\Entity\SubCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class SheetType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration("Titre", "Entrez le titre de la fiche"))
            ->add('organization', TextType::class, $this->getConfiguration("Organisme", "Entrez le nom de l'organisme (optionnel)"))
            ->add('content', TextareaType::class, $this->getConfiguration("Contenu", "Ici le contenu de la fiche"))            
            ->add('subCategory', EntityType::class, [
                'class' => SubCategory::class,
                'choice_label' => function($subCategory){
                    return $subCategory->getTitle();
    
                },
                'group_by' => function($subCategory){
                    return $subCategory->getCategory()->getTitle();
    
                }
            ])

            ->add(
                'headers',
                CollectionType::class,[
                    
                 'entry_type' => HeaderType::class,
                 'allow_add' => true,
                 'allow_delete' => true
 
                ]
            )
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sheet::class,
        ]);
    }
}

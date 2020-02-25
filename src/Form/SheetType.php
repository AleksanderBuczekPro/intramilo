<?php

namespace App\Form;

use App\Entity\Sheet;
use App\Form\ToolsType;
use App\Entity\Category;
use App\Entity\Document;
use App\Form\HeaderType;
use App\Form\SectionType;
use App\Entity\SubCategory;
use App\Repository\SheetRepository;
use App\Repository\DocumentRepository;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
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
            ->add('content', CKEditorType::class, $this->getConfiguration("Contenu", "Ici le contenu de la fiche"))            
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
                 'label' => "Coordonnées",
                 'entry_type' => HeaderType::class,
                 'allow_add' => true, 
                 'allow_delete' => true,

                 'prototype_name' => "__h__"
 
                ])
            
                ->add('tool', EntityType::class, [
                    "label" => "Pièces jointes (fiches)",
                    'class' => Sheet::class,
                    'choice_label' => 'title',
                    'multiple' => true,
                    'required' => false,
    
                    'query_builder' => function (SheetRepository $sr) {
                        return $sr->createQueryBuilder('s')
                            ->orderBy('s.title', 'ASC');
                    },
                    
                    'group_by' => function($tool){
                        return $tool->getSubCategory()->getTitle();
        
                    }
                ])
                ->add('sheetDocuments', EntityType::class, [
                    "label" => "Pièces jointes (documents)",
                    'class' => Document::class,
                    'choice_label' => 'title',
                    'multiple' => true,
                    'required' => false,
    
                    'query_builder' => function (DocumentRepository $dr) {
                        return $dr->createQueryBuilder('d')
                            ->orderBy('d.title', 'ASC');
                    },
                    
                    'group_by' => function($attachment){
                        return $attachment->getSubCategory()->getTitle();
    
                    }
                ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sheet::class,
        ]);
    }
}

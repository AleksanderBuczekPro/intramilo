<?php

namespace App\Form;

use App\Entity\Sheet;
use App\Form\ToolsType;
use App\Entity\Category;
use App\Entity\Document;
use App\Form\HeaderType;
use App\Form\SectionType;
use App\Entity\SubCategory;
use App\Entity\Organization;
use App\Repository\SheetRepository;
use App\Repository\DocumentRepository;
use Symfony\Component\Form\AbstractType;
use App\Repository\SubCategoryRepository;
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

        $user = $options['user'];

        $builder
            ->add('title', TextType::class, $this->getConfiguration("Titre", "Entrez le titre de la fiche"))
            ->add('organization', EntityType::class, [
                'label' => "Organisme",
                'placeholder' => "Sélectionnez un organisme",
                'class' => Organization::class,
                'choice_label' => function($organization){
                    return $organization->getName();
                }
            ])
            ->add('content', CKEditorType::class, $this->getConfiguration("Texte", "Ici le contenu de la fiche"))            
            ->add('subCategory', EntityType::class,[
                'label' => "Sous-catégorie",
                'class' => SubCategory::class,
                'query_builder' => function (SubCategoryRepository $sr) use($user) {
                    return $sr->createQueryBuilder('s')
                              ->select('s')
                              ->join('s.authors', 'a')
                              ->where('a.id = :authorId')
                              ->setParameter('authorId', $user->getId());
                },
                'group_by' => function($subCategory){
                    return $subCategory->getCategory()->getTitle();
    
                }
            ])

            ->add(
                'headers',
                CollectionType::class,[
                 'label' => "Titre de l'entête",
                 'entry_type' => HeaderType::class,
                 'allow_add' => true,
                 'allow_delete' => true,

                 'prototype_name' => "__h__"
 
                ])

                // ->add(
                //     'attachments',
                //     CollectionType::class,[
                //      'label' => "Pièces jointes",
                //      'entry_type' => AttachmentType::class,
                //      'allow_add' => true,
                //      'allow_delete' => true,
    
                //      'prototype_name' => "__h__"
     
                //     ])
            
                ->add('tool', EntityType::class, [
                    "label" => "Fiches existantes",
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
                    "label" => "Documents existants",
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
            'user' => null
        ]);
    }
}

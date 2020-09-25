<?php

namespace App\Form;

use App\Entity\Sheet;
use App\Form\ToolsType;
use App\Entity\Category;
use App\Entity\Document;
use App\Form\HeaderType;
use App\Entity\Paragraph;
use App\Form\SectionType;
use App\Entity\SubCategory;
use App\Entity\Organization;
use App\Repository\SheetRepository;
use App\Repository\DocumentRepository;
use App\Repository\OrganizationRepository;
use Symfony\Component\Form\AbstractType;
use App\Repository\SubCategoryRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class SheetType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $user = $options['user'];

        $builder
            ->add('title', TextareaType::class, $this->getConfiguration("Titre", "Sans titre", [
                'required' => false,
                'empty_data' => 'Sans titre',
                'attr' => [
                    
                    'maxlength' => 255

                ]
            ]))
            ->add('subtitle', TextType::class, $this->getConfiguration("Sous-titre", "Sans complément de titre", [
                'required' => false
            ]))
            ->add('comment', TextareaType::class, $this->getConfiguration("Commentaire", "Renseignez les éléments à corriger", [
                'required' => false
            ]))
            ->add('introduction',  CKEditorType::class, $this->getConfiguration("Introduction", "Introduction de la fiche"))
            ->add('organization', EntityType::class, [
                'required' => false,
                'label' => "Organisme",
                'placeholder' => "Sélectionnez un organisme",
                'class' => Organization::class,
                'choice_label' => function($user){
                    return $user->getName();
                },
                'query_builder' => function (OrganizationRepository $or) {
                    return $or->createQueryBuilder('o')
                        ->orderBy('o.name', 'ASC');
                },
                'choice_attr' => function($organization){
                    return [
                        'data-address' => $organization->getFullAddress(),
                        'data-phone' => $organization->getPhoneNumber(),
                        'data-email' => $organization->getEmail(),
                        'data-website' => $organization->getWebsite()
                    ];
                }

                
            ])
            // ->add('content', CKEditorType::class, $this->getConfiguration("Texte", "Ici le contenu de la fiche"))

            ->add(
                'paragraphs',
                CollectionType::class,[
                 'label' => "Section",
                 'entry_type' => ParagraphType::class,
                 'allow_add' => true,
                 'allow_delete' => true,

                 'prototype_name' => "__p__"
 
                ])

            ->add('subCategory', EntityType::class,[
                'label' => "Sous-catégorie",
                'class' => SubCategory::class,
                'query_builder' => function (SubCategoryRepository $sr) use($user) {

                    // dump($options['roles']);

                    
                if (in_array('ROLE_ADMIN', $user->getRoles())) {
                    
                    return $sr->createQueryBuilder('s')
                              ->select('s');

                }else{
                    
                    return $sr->createQueryBuilder('s')
                              ->select('s')
                              ->join('s.authors', 'a')
                              ->where('a.id = :authorId')
                              ->setParameter('authorId', $user->getId());

                }

                    
                },
                'group_by' => function($subCategory){
                    return $subCategory->getCategory()->getTitle();
    
                }
            ])

            ->add(
                'headers',
                CollectionType::class,[
                 'label' => "Entête",
                 'entry_type' => HeaderType::class,
                 'allow_add' => true,
                 'allow_delete' => true,

                 'prototype_name' => "__h__"
 
                ])

            ->add(
                'attachments',
                CollectionType::class,[
                    'label' => "Pièces jointes",
                    'attr' => ['class' => "row"],
                    'entry_type' => AttachmentType::class,
                    'allow_add' => true,
                    'allow_delete' => false,

                    'prototype_name' => "__a__"
                ])
            
                // ->add('tool', EntityType::class, [
                //     "label" => "Fiches existantes",
                //     'class' => Sheet::class,
                //     'choice_label' => 'title',
                //     'multiple' => true,
                //     'required' => false,
    
                //     'query_builder' => function (SheetRepository $sr) {
                //         return $sr->createQueryBuilder('s')
                //             ->orderBy('s.title', 'ASC');
                //     },
                    
                //     'group_by' => function($tool){
                //         return $tool->getSubCategory()->getTitle();
        
                //     }
                // ])
                // ->add('sheetDocuments', EntityType::class, [
                //     "label" => "Documents existants",
                //     'class' => Document::class,
                //     'choice_label' => 'title',
                //     'multiple' => true,
                //     'required' => false,
    
                //     'query_builder' => function (DocumentRepository $dr) {
                //         return $dr->createQueryBuilder('d')
                //             ->orderBy('d.title', 'ASC');
                //     },
                    
                //     'group_by' => function($attachment){
                //         return $attachment->getSubCategory()->getTitle();
    
                //     }
                // ])
                ->add('pic', FileType::class, [

                    // 'label' => 'Photo de profil (fichier Image)',
                    'label' => "Image de fond",
    
                    // unmapped means that this field is not associated to any entity property
                    'mapped' => false,
    
                    // make it optional so you don't have to re-upload the PDF file
                    // everytime you edit the Product details
                    'required' => false,
    
                    // unmapped fields can't define their validation using annotations
                    // in the associated entity, so you can use the PHP constraint classes
                    'constraints' => [
                        new Image([
                            'maxSize' => '50M',
                            'mimeTypes' => [
                                'image/jpeg'
                            ],
                            'mimeTypesMessage' => 'Merci de charger une image JPEG.',
                            'minHeight' => 125,
                            'minHeightMessage' => 'Merci de charger une image d\'une hauteur de 125 pixels minimum.',
                            'minWidth' => 125,
                            'minWidthMessage' => 'Merci de charger une image d\'une largeur de 125 pixels minimum.'
                        ])
                    ],
                ])
                ->add('save', SubmitType::class, [
                    'label' => 'Oui',
                    'attr' => [
                        'id' => 'send-btn',
                        'class' => 'btn btn-my-primary']
                    
                ])
                ->add('saveDraft', SubmitType::class, [
                    'label' => 'Enregistrer en brouillon',
                    'attr' => [
                        'class' => 'btn btn-my-dark dark pl-4',
                        'data-toggle' => 'modal',
                        'data-target' => '#draftModal'                                     
                        ]
                ])
                ->add('saveDraftExit', SubmitType::class, [
                    'label' => 'Enregistrer en brouillon et Quitter',
                    'attr' => [
                        'class' => 'btn dark w-100 px-4',
                        'data-toggle' => 'modal',
                        'data-target' => '#draftModal'                  
                        ]
                ])
                ->add('sendToCorrect', SubmitType::class, [
                    'label' => 'Envoyer à corriger',
                    'attr' => [
                        'class' => 'btn btn-my-danger small px-3 mr-2 float-right'             
                        ]
                ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sheet::class,
            'user' => null,
            'allow_extra_fields' => true
        ]);
    }
}

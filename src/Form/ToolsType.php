<?php

namespace App\Form;

use App\Entity\Sheet;
use App\Entity\Document;
use App\Form\AttachmentType;
use App\Repository\SheetRepository;
use App\Repository\DocumentRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ToolsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tool', EntityType::class, [
                'class' => Sheet::class,
                'choice_label' => 'title',
                'multiple' => true,

                'query_builder' => function (SheetRepository $sr) {
                    return $sr->createQueryBuilder('s')
                        ->orderBy('s.title', 'ASC');
                },
                
                'group_by' => function($tool){
                    return $tool->getSubCategory()->getTitle();
    
                }
            ])
            ->add('sheetDocuments', EntityType::class, [
                'class' => Document::class,
                'choice_label' => 'title',
                'multiple' => true,

                'query_builder' => function (DocumentRepository $dr) {
                    return $dr->createQueryBuilder('d')
                        ->orderBy('d.title', 'ASC');
                },
                
                'group_by' => function($attachment){
                    return $attachment->getSubCategory()->getTitle();

                }
            ])
            ->add(
                'attachments',
                CollectionType::class,[
                
                'entry_type' => AttachmentType::class,
                'allow_add' => true, 
                'allow_delete' => true
 
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

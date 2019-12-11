<?php

namespace App\Form;

use App\Entity\Sheet;
use App\Entity\Document;
use App\Repository\DocumentRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SheetDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sheetDocuments', EntityType::class, [
                'class' => Document::class,
                'choice_label' => 'title',
                'multiple' => true,

                'query_builder' => function (DocumentRepository $dr) {
                    return $dr->createQueryBuilder('d')
                        ->orderBy('d.title', 'ASC');
                },
                
                'group_by' => function($sheetDocument){
                    return $sheetDocument->getSubCategory()->getTitle();

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

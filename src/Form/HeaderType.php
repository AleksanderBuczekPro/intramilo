<?php

namespace App\Form;

use App\Entity\Header;
use App\Form\SectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class HeaderType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration("Titre", "Entête sans titre"))
            ->add(
                'sections',
                CollectionType::class,[
                    
                 'entry_type' => SectionType::class,
                 'allow_add' => true,
                 'allow_delete' => true,

                 'prototype_name' => "__s__"
  
                ]
            )
        ;
    }
 
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Header::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Paragraph;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ParagraphType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration("Titre", "Section sans titre", [
                'required' => false,
                'empty_data' => 'Section sans titre'
            ]))
            ->add('content', CKEditorType::class, $this->getConfiguration("Texte", "Ici le contenu de la section", [
                'required' => false,
                'empty_data' => 'Aucune information renseignée'
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Paragraph::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Document;
use Symfony\Component\Form\AbstractType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DocumentType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration("Titre", "Entrez le titre de la fiche"))
            ->add('organization', TextType::class, $this->getConfiguration("Organisme", "Entrez le nom de l'organisme (optionnel)"))
        ;

        $builder->add('genericFile', VichFileType::class,[
            'required' => false,
            'allow_delete' => false,
            'download_uri' => true,
            'download_label' => false,
            'asset_helper' => true,

            'label' => 'Votre fichier'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }
}

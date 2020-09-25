<?php

namespace App\Form;

use App\Entity\Attachment;
use App\Entity\Organization;

use Symfony\Component\Form\AbstractType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AttachmentType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration("Titre", "Titre de la pièce jointe"), [
                'required' => false
            ])
            // ->add('organization', EntityType::class, [
            //     'label' => "Organisme",
            //     'placeholder' => "Sélectionnez un organisme",
            //     'class' => Organization::class,
            //     'choice_label' => function($organization){
            //         return $organization->getName();
            //     }
            // ])
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
            'data_class' => Attachment::class,
        ]);
    }
}

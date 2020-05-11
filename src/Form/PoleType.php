<?php

namespace App\Form;

use App\Entity\Pole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PoleType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration("Nom du pôle", "Entrez le nom du pôle ici"))
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
                        'mimeTypesMessage' => 'Merci de charger une image.',
                        'minHeight' => 125,
                        'minHeightMessage' => 'Merci de charger une image d\'une hauteur de 125 pixels minimum.',
                        'minWidth' => 125,
                        'minWidthMessage' => 'Merci de charger une image d\'une largeur de 125 pixels minimum.'
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pole::class,
        ]);
    }
}

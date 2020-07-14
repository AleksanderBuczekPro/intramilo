<?php

namespace App\Form;

use App\Entity\Organization;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class OrganizationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, $this->getConfiguration("Nom de l'organisme", "Entrez le nom de l'organisme"))
            ->add('address', TextType::class, $this->getConfiguration("Adresse", "Adresse", [
                'required' => false
            ]))
            ->add('postcode', NumberType::class, $this->getConfiguration("Code postal", "CP", [
                'required' => false
            ]))
            ->add('city', TextType::class, $this->getConfiguration("Ville", "Ville", [
                'required' => false
            ]))
            ->add('phoneNumber', TelType::class, $this->getConfiguration("Téléphone", "Numéro de téléphone", [
                'required' => false
            ]))
            ->add('email', EmailType::class, $this->getConfiguration("Email", "Email", [
                'required' => false
            ]))
            ->add('website', UrlType::class, $this->getConfiguration("Site web", "Copiez-collez l'URL du site web", [
                'required' => false
            ]))
            ->add('logo', FileType::class, [

                // 'label' => 'Photo de profil (fichier Image)',
                'label' => "Logo de l'organisme",

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
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Merci de charger une image.',
                        'minHeight' => 75,
                        'minHeightMessage' => 'Merci de charger une image d\'une hauteur de 75 pixels minimum.',
                        'minWidth' => 75,
                        'minWidthMessage' => 'Merci de charger une image d\'une largeur de 75 pixels minimum.'
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Organization::class,
        ]);
    }
}

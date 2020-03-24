<?php

namespace App\Form;

use App\Entity\Organization;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class OrganizationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, $this->getConfiguration("Nom de l'organisme", "Entrez le nom de l'organisme"))
            ->add('address', TextType::class, $this->getConfiguration("Adresse", "Adresse"))
            ->add('postcode', NumberType::class, $this->getConfiguration("Code postal", "CP"))
            ->add('city', TextType::class, $this->getConfiguration("Ville", "Entrez la ville"))
            ->add('phoneNumber', TextType::class, $this->getConfiguration("Téléphone", "Entrez le numéro de téléphone de l'organisme"), [
                'required' => false
            ])
            ->add('email', EmailType::class, $this->getConfiguration("Email", "Entrez le mail de l'organisme"), [
                'required' => false
            ])
            ->add('website', UrlType::class, $this->getConfiguration("Site web", "Copiez-collez l'URL du site web de l'organisme"), [
                'required' => false
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

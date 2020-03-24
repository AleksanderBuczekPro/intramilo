<?php

namespace App\Form;

use App\Entity\Antenne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class AntenneType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration("Nom", "Entrez le nom de l'antenne"))
            ->add('address', TextType::class, $this->getConfiguration("Adresse", "Adresse"))
            ->add('postcode', NumberType::class, $this->getConfiguration("Code postal", "CP"))
            ->add('city', TextType::class, $this->getConfiguration("Ville", "Entrez la ville"))
            ->add('phoneNumber', TextType::class, $this->getConfiguration("Téléphone", "Entrez le numéro de téléphone de l'antenne"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Antenne::class,
        ]);
    }
}

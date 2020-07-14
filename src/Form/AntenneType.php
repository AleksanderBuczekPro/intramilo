<?php

namespace App\Form;

use App\Entity\Antenne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
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
            ->add('latitude', NumberType::class, $this->getConfiguration("Latitude", "47.333498", [
                'scale' => 6
            ]))
            ->add('longitude', NumberType::class, $this->getConfiguration("Longitude", "5.068202", [
                'scale' => 6
            ]))
            ->add('phoneNumber', TelType::class, $this->getConfiguration("Téléphone", "Entrez le numéro de téléphone de l'antenne"))
            ->add('hours', TextType::class, $this->getConfiguration("Horaires", "8h15 - 12h15 / 13h00 - 17h00", [
                "required" => false
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Antenne::class,
        ]);
    }
}

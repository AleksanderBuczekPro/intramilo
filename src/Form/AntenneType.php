<?php

namespace App\Form;

use App\Entity\Antenne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
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

            ->add('MondayAmOpen', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))
            ->add('MondayAmClose', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))
            ->add('MondayPmOpen', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))
            ->add('MondayPmClose', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))

            ->add('TuesdayAmOpen', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))
            ->add('TuesdayAmClose', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))
            ->add('TuesdayPmOpen', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))
            ->add('TuesdayPmClose', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))

            ->add('WednesdayAmOpen', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))
            ->add('WednesdayAmClose', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))
            ->add('WednesdayPmOpen', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))
            ->add('WednesdayPmClose', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))

            ->add('ThursdayAmOpen', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))
            ->add('ThursdayAmClose', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))
            ->add('ThursdayPmOpen', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))
            ->add('ThursdayPmClose', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))

            ->add('FridayAmOpen', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))
            ->add('FridayAmClose', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))
            ->add('FridayPmOpen', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))
            ->add('FridayPmClose', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))

            ->add('SaturdayAmOpen', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))
            ->add('SaturdayAmClose', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))
            ->add('SaturdayPmOpen', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))
            ->add('SaturdayPmClose', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))

            ->add('SundayAmOpen', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))
            ->add('SundayAmClose', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))
            ->add('SundayPmOpen', TimeType::class, $this->getConfiguration(false, "", [
                "required" => false
            ]))
            ->add('SundayPmClose', TimeType::class, $this->getConfiguration(false, "", [
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

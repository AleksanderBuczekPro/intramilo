<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AccountType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('introduction', TextType::class, $this->getConfiguration("Domaine de compétence (optionnel)", "Bâtiment, Commerce, Numérique...", [
                'required' => false
            ]))
            ->add('phoneNumber', TelType::class, $this->getConfiguration("Téléphone", "Numéro de téléphone externe", [
                'required' => false
            ]))
            ->add('directNumber', TelType::class, $this->getConfiguration("Ligne directe", "Ligne directe à 3 chiffres", [
                'required' => false
            ]))
            ->add('proNumber', TelType::class, $this->getConfiguration("Téléphone professionnel", "Numéro de téléphone professionnel", [
                'required' => false
            ]))

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

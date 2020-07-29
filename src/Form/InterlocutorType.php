<?php

namespace App\Form;

use App\Entity\Interlocutor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class InterlocutorType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfiguration("Prénom", "Prénom"))
            ->add('lastName', TextType::class, $this->getConfiguration("Nom", "Nom"))
            ->add('function', TextType::class, $this->getConfiguration("Fonction", "Fonction"))
            ->add('phoneNumber', TelType::class, $this->getConfiguration("Numéro de téléphone 1", "Téléphone 1", [

                'required' => false

            ]))
            ->add('proNumber', TelType::class, $this->getConfiguration("Numéro de téléphone 2", "Téléphone 2", [

                'required' => false

            ]))
            ->add('email', EmailType::class, $this->getConfiguration("Email", "Email", [
                
                'required' => false

            ]))
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Interlocutor::class,
        ]);
    }
}

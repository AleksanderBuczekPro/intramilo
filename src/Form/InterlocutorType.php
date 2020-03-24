<?php

namespace App\Form;

use App\Entity\Interlocutor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class InterlocutorType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfiguration("Prénom", "Entrez le nom de l'interlocuteur"))
            ->add('lastName', TextType::class, $this->getConfiguration("Nom", "Entrez le nom de l'interlocuteur"))
            ->add('function', TextType::class, $this->getConfiguration("Fonction", "Entrez la fonction de l'interlocuteur"))
            ->add('phoneNumber', TextType::class, $this->getConfiguration("Numéro de téléphone", "Entrez le numéro de téléphone de l'interlocuteur"))
            ->add('email', EmailType::class, $this->getConfiguration("Email", "Entrez l'email de l'interlocuteur'"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Interlocutor::class,
        ]);
    }
}

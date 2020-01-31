<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use App\Entity\Poste;
use App\Entity\Groupe;
use App\Entity\Antenne;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends ApplicationType
{

    

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfiguration("Prénom", "Prénom"))
            ->add('lastName', TextType::class, $this->getConfiguration("Nom", "Nom de famille"))
            ->add('email', EmailType::class, $this->getConfiguration("Email", "Adresse email"))
            ->add('phoneNumber', TextType::class, $this->getConfiguration("Téléphone", "Numéro de téléphone"))
            // ->add('picture', UrlType::class, $this->getConfiguration("Photo de profil", "URL de la photo de profil"))
            ->add('hash', PasswordType::class, $this->getConfiguration("Mot de passe", "Choissisez un bon mot de passe"))
            ->add('passwordConfirm', PasswordType::class, $this->getConfiguration("Confirmation de mot de passe", "Veuillez confirmer votre mot de passe"))
            // ->add('introduction', TextType::class, $this->getConfiguration("Introduction", "Présentez vous en quelques mots..."))
            // ->add('description', TextareaType::class, $this->getConfiguration("Description détaillée", "C'est le moment de vous présenter en détail"))
            ->add('antenne', EntityType::class, [
                'class' => Antenne::class,
                'choice_label' => function($antenne){
                    return $antenne->getTitle();
    
                }
            ])
            ->add('groupe', EntityType::class, [
                'class' => Groupe::class,
                'choice_label' => function($groupe){
                    return $groupe->getTitle();
    
                }
            ])
            ->add('poste', EntityType::class, [
                'class' => Poste::class,
                'choice_label' => function($poste){
                    return $poste->getTitle();
    
                }
            ])
            ->add('userRoles', EntityType::class, [
                'class' => Role::class,
                'choice_label' => function($role){
                    return $role->getTitle();
                },
                'multiple' => true,
                'expanded' => true
            ])
            ;
        }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

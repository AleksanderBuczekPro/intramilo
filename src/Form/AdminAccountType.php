<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use App\Entity\Poste;
use App\Entity\Groupe;
use App\Entity\Antenne;
use App\Form\ApplicationType;
use App\Repository\PosteRepository;
use App\Repository\GroupeRepository;
use App\Repository\AntenneRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdminAccountType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            

        ->add('firstName', TextType::class, $this->getConfiguration("Prénom", "Prénom"))
            ->add('lastName', TextType::class, $this->getConfiguration("Nom", "Nom de famille"))
            ->add('email', EmailType::class, $this->getConfiguration("Email", "Adresse email"))
            ->add('phoneNumber', TelType::class, $this->getConfiguration("Téléphone", "Numéro de téléphone externe", [
                'required' => false
            ]))
            ->add('directNumber', TelType::class, $this->getConfiguration("Ligne directe", "Ligne directe à 3 chiffres", [
                'required' => false
            ]))
            ->add('proNumber', TelType::class, $this->getConfiguration("Téléphone professionnel", "Numéro de téléphone professionnel", [
                'required' => false
            ]))
            ->add('introduction', TextType::class, $this->getConfiguration("Domaine de compétence (optionnel)", "Bâtiment, Commerce, Numérique...", [
                'required' => false
            ]))
            ->add('antenne', EntityType::class, [
                'label' => "Antenne",
                'class' => Antenne::class,
                'choice_label' => function($antenne){
                    return $antenne->getTitle();
    
                },
                'query_builder' => function (AntenneRepository $ar) {
                    return $ar->createQueryBuilder('a')
                        ->orderBy('a.title', 'ASC');
                }
            ])
            ->add('groupe', EntityType::class, [
                'label' => "Groupe",
                'class' => Groupe::class,
                'choice_label' => function($groupe){
                    return $groupe->getTitle();
    
                },
                'query_builder' => function (GroupeRepository $gr) {
                    return $gr->createQueryBuilder('g')
                        ->orderBy('g.title', 'ASC');
                }
            ])
            ->add('poste', EntityType::class, [
                'label' => "Poste",
                'class' => Poste::class,
                'choice_label' => function($poste){
                    return $poste->getTitle();
    
                },
                'query_builder' => function (PosteRepository $pr) {
                    return $pr->createQueryBuilder('p')
                        ->orderBy('p.title', 'ASC');
                }
            ]);
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => false
        ]);
    }
}

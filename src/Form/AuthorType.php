<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Sheet;
use App\Form\ApplicationType;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('author', EntityType::class, [
                'label' => "Nouveau",
                'placeholder' => "Sélectionnez un auteur",
                'class' => User::class,
                'query_builder' => function (UserRepository $ur){
                    return $ur->createQueryBuilder('u')
                              ->select('u')
                              ->orderBy('u.lastName', 'ASC');
                }
                ,
                'choice_label' => function($user){
                    return $user->getlastName() . ' ' . $user->getfirstName();
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sheet::class,
        ]);
    }
}

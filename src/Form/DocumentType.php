<?php

namespace App\Form;

use App\Entity\Document;
use App\Entity\SubCategory;
use App\Entity\Organization;
use Symfony\Component\Form\AbstractType;
use App\Repository\SubCategoryRepository;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DocumentType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];

        $builder
            ->add('subCategory', EntityType::class,[
                'label' => "Sous-catégorie",
                'class' => SubCategory::class,
                'query_builder' => function (SubCategoryRepository $sr) use($user) {
                    return $sr->createQueryBuilder('s')
                            ->select('s')
                            ->join('s.authors', 'a')
                            ->where('a.id = :authorId')
                            ->setParameter('authorId', $user->getId());
                },
                'group_by' => function($subCategory){
                    return $subCategory->getCategory()->getTitle();

                }
            ])

            ->add('title', TextType::class, $this->getConfiguration("Titre", "Entrez le titre de la fiche"))
            ->add('organization', EntityType::class, [
                'label' => "Organisme",
                'placeholder' => "Sélectionnez un organisme",
                'class' => Organization::class,
                'choice_label' => function($organization){
                    return $organization->getName();
                }
            ])
        ;

        $builder->add('genericFile', VichFileType::class,[
            'required' => false,
            'allow_delete' => false,
            'download_uri' => true,
            'download_label' => false,
            'asset_helper' => true,

            'label' => 'Votre fichier'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
            'user' => null
        ]);
    }
}

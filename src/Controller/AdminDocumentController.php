<?php

namespace App\Controller;

use App\Entity\Sheet;
use App\Service\Filter;
use App\Repository\UserRepository;
use App\Repository\SheetRepository;
use App\Repository\GroupeRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SubCategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDocumentController extends AbstractController
{
    /**
     * Permet d'afficher la liste des document à valider par un responsable
     * 
     * @Route("/admin/document", name="admin_documents")
     */
    public function index(SheetRepository $sheetRepo, DocumentRepository $docRepo, UserRepository $userRepo, SubCategoryRepository $subRepo, Request $request, EntityManagerInterface $manager, Filter $filter)
    {
        // Recherche des groupes dont l'utilisateur est responsable
        $groupes = $this->getUser()->getAdminGroupes();

        dump($groupes);

        // Recherche des utilisateurs appartenant à ces groupes
        $users = [];
        foreach ($groupes as $groupe) {
            $user = $userRepo->findByGroupe($groupe);
            $users = array_merge_recursive($users, $user);
        }

        // Recherche des sous-catégories que le responsable doit vérifier
        $subCategories = [];
        foreach ($users as $user) {
            $subCategory = $subRepo->findByAuthor($user);
            $subCategories = array_merge_recursive($subCategories, $subCategory);
        }


        // $subCategories = $this->getUser()->getSubCategories();

        $files = $filter->getFiles($subCategories);

        dump($files);

        return $this->render('admin/document/index.html.twig', [

            'filesToValidate' => $files['filesToValidate'],
            'filesToCorrect' => $files['filesToCorrect'],
            'filesUpToDate' => $files['filesUpToDate'],
            'filesWellObsolete' => $files['filesWellObsolete'],
            'filesObsolete' => $files['filesObsolete'],
            'subCategories' => $subCategories,
            'groupes' => $groupes,
            'users' => $users
        ]);

    }


    /**
     * Permet d'envoyer une fiche à corriger
     *
     * @return void
     */
    public function toCorrect(){


    }
}

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

        $files = $filter->getAdminFiles($this->getUser(), $userRepo, $sheetRepo);
      

        return $this->render('admin/document/index.html.twig', [

            'filesToValidate' => $files['filesToValidate'],
            'filesToCorrect' => $files['filesToCorrect'],
            'filesUpToDate' => $files['filesUpToDate'],
            'filesWellObsolete' => $files['filesWellObsolete'],
            'filesObsolete' => $files['filesObsolete']

        ]);

    }

    /**
     * Permet de faire la liste des dossiers à valider par un responsable
     * 
     * @Route("/admin/folders", name="admin_folders")
     * 
     * @return Response
     */
    public function myFolders(SheetRepository $sheetRepo, UserRepository $userRepo)
    {
        $responsable = $this->getUser();

         // Recherche des GROUPES dont l'utilisateur est responsable
         $groupes = $responsable->getAdminGroupes();

         // Recherche des UTILISATEURS qui font partie de ce groupe
         $users = [];
         foreach ($groupes as $groupe) {
             $user = $userRepo->findByGroupe($groupe);
             $users = array_merge_recursive($users, $user);
         }
 
         // Recherche des FICHES des utilisateurs
         $subCategories = [];
         foreach ($users as $user) {

            $subs = $user->getSubCategories();

            foreach($subs as $sub){
                
                if(!in_array($sub, $subCategories)){

                    // $subCategories = array_merge_recursive($subCategories, $sub);
                    $subCategories[] = $sub;
                }

            }
 
         }

        return $this->render('admin/documentation/folders.html.twig', [

            'subCategories' => $subCategories
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

<?php

namespace App\Controller;

use App\Entity\Sheet;
use App\Service\Filter;
use App\Repository\UserRepository;
use App\Repository\SheetRepository;
use App\Repository\GroupeRepository;
use App\Repository\DocumentRepository;
use App\Repository\SubCategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
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
    public function index(SheetRepository $sheetRepo, DocumentRepository $docRepo, UserRepository $userRepo, SubCategoryRepository $subRepo, Request $request, ObjectManager $manager, Filter $filter)
    {
        // Recherche des groupes dont l'utilisateur est responsable
        $groupes = $this->getUser()->getAdminGroupes();

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


        $f = $request->query->get('filter');

        dump($f);

        // Si il y a un filtre
        if(isset($f) && $f != "all"){

            $files = $filter->getResults($f, $subCategories);

        }else{

            // Récupération des fiches
            $sheets = [];
            foreach ($subCategories as $subCategory) {
                $sheet = $sheetRepo->findBySubCategory($subCategory);
                $sheets = array_merge_recursive($sheets, $sheet);
            }

            // Récupération des documents
            $documents = [];
            foreach ($subCategories as $subCategory) {
                $document = $docRepo->findBySubCategory($subCategory);
                $documents = array_merge_recursive($documents, $document);
            }

            $files = array_merge_recursive($sheets, $documents);

            usort($files, function($a, $b){ 
                return strcasecmp($a->getTitle(), $b->getTitle());
            });
        
        }

        

        return $this->render('admin/document/index.html.twig', [
            'groupes' => $groupes,
            'users' => $users,
            'subCategories' => $subCategories,
            'files' => $files,
        ]);
    }

    /**
     * Permet de valider une fiche "En cours de validation"
     * 
     * @Route("/admin/document/validate/{id}", name="admin_document_validate")
     * 
     * @IsGranted("ROLE_USER")
     *
     * @return void
     */
    public function validate(Sheet $sheet, ObjectManager $manager, Request $request){

        // On récupère l'ancienne fiche
        $oldSheet = $sheet->getOrigin();

        // On initilaise les paramètres
        $sheet->setOrigin(null);
        $sheet->setStatus(null);

        dump($oldSheet);

        // On supprime l'ancienne fiche
        if($oldSheet != null){

            $manager->remove($oldSheet);

        }
       
        $manager->flush();

        $subCategory = $sheet->getSubCategory();
        $category = $subCategory->getCategory();

        return $this->redirectToRoute('doc_show', ['slug' => $category->getSlug(), 'sub_slug' => $subCategory->getSlug()]);

    }

    /**
     * Permet d'envoyer une fiche à corriger
     *
     * @return void
     */
    public function toCorrect(){


    }
}

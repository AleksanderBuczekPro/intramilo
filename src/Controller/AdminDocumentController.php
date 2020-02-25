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
     * Permet de valider une fiche "En cours de validation"
     * 
     * @Route("/admin/document/validate/", name="admin_document_validate")
     *  
     * 
     * @IsGranted("ROLE_USER")
     *
     * @return void
     */
    public function validate(ObjectManager $manager, Request $request, SheetRepository $repo){

        dump($request);

        $id = $request->request->get('id');
        $sheet = $repo->findOneById($id);

        dump($id);

        // On récupère l'ancienne fiche
        $oldSheet = $sheet->getOrigin();

        // On initilaise les paramètres
        $sheet->setOrigin(null);
        $sheet->setStatus(null);

        dump($request->request->get('content'));

        // On remplace le texte par le texte formaté (sans couleurs)
        $sheet->setContent($request->request->get('content'));

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
